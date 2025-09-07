<?php

namespace App\Services;

use Amazon\Pay\API\Client;
use Amazon\Pay\API\Constants\Environment;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Delivery;


class AmazonPayService
{
    protected $client;
    protected $config;

    public function __construct()
    {
        $this->config = [
            'public_key_id' => config('amazonpay.public_key_id'),
            'private_key' => config('amazonpay.private_key_path'),
            'region' => config('amazonpay.region'),
            'sandbox' => config('amazonpay.sandbox'),
        ];

        $this->client = new Client($this->config);
    }


    /**
     * 売上確定（Capture）
     */
    public function captureCharge(string $authorizationId, int $amount): array
    {
        $response = $this->client->captureCharge(
            $authorizationId, // ✅ Authorization ID を渡す
            [
                'captureAmount' => [
                    'amount'       => $amount,
                    'currencyCode' => 'JPY',
                ],
            ],
            [] // options
        );

        return json_decode($response['response']['body'], true);
    }


    /*AmazonPayService で与信レスポンスから chargeId を取得*/
    public function authorizeCharge(string $chargePermissionId, int $amount): array
    {
        $response = $this->client->authorizeCharge(
            $chargePermissionId,
            [
                'authorizationReferenceId' => uniqid('auth_'),
                'chargeAmount' => [
                    'amount'       => $amount,
                    'currencyCode' => 'JPY',
                ],
                'captureNow' => false, // ここで即売上にしない
            ],
            []
        );

        // レスポンスの JSON を decode
        $data = json_decode($response['response']['body'], true);

        // chargeId を返す
        return [
            'chargeId' => $data['chargeId'] ?? null,
            'status'   => $data['statusDetails']['state'] ?? null,
            'raw'      => $data,
        ];
    }




    public function getCharge(string $chargeId): array
    {
        return $this->client->getCharge($chargeId);
    }
    /**
     * 決済セッションを作成
     */
    public function createSession($amount, $merchantReferenceId = null)
    {
        $merchantReferenceId = $merchantReferenceId ?: 'Order_' . time();

        // セッションに金額を保存（セキュリティのため）
        session(['payment_amount' => $amount]);

        $payload = [
            'webCheckoutDetails' => [
                //'checkoutResultReturnUrl' => route('amazon-pay.complete'),
                'checkoutResultReturnUrl' => route('amazon-pay.complete') . '?amazonCheckoutSessionId={checkoutSessionId}',
                'checkoutCancelUrl' => route('amazon-pay.cancel'),
                'checkoutMode' => 'ProcessOrder',
            ],
            'storeId' => config('amazonpay.store_id'),
            'chargePermissionType' => 'OneTime',
            'merchantMetadata' => [
                'merchantReferenceId' => $merchantReferenceId,
                'merchantStoreName' => config('amazonpay.store_name'),
                'noteToBuyer' => '料金のお支払いです',
            ],
            'paymentDetails' => [
                'paymentIntent' => 'AuthorizeWithCapture',
                'chargeAmount' => [
                    'amount' => (string)$amount,
                    'currencyCode' => 'JPY',
                ],
            ],
            'scopes' => ['name', 'email'],
        ];

        $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $signature = $this->client->generateButtonSignature($payloadJson);

        return [
            'payloadJson' => $payloadJson,
            'signature' => $signature,
            'publicKeyId' => config('amazonpay.public_key_id'),
            'merchantId' => config('amazonpay.merchant_id'),
            'sandbox' => config('amazonpay.sandbox'),
        ];
    }



    public function completePayment(string $amazonCheckoutSessionId, float $amount): array
    {
        \Log::info('AmazonPay completePayment() 開始', [
            'amazonCheckoutSessionId' => $amazonCheckoutSessionId
        ]);

        try {
            $idempotencyKey = uniqid('amazonpay_', true);

            // ✅ 修正ポイント
            // headers は第3引数で渡す。第2引数はリクエストボディ（必要なければ空配列）
            $response = $this->client->completeCheckoutSession(
                $amazonCheckoutSessionId,
                [
                    'paymentDetails' => [
                        'chargeAmount' => [
                            'amount' => $amount,
                            'currencyCode' => 'JPY',
                        ],
                    ],
                ],
                [
                    'x-amz-pay-idempotency-key' => $idempotencyKey,
                ]
            );

            \Log::info('AmazonPay completePayment() 結果', ['raw' => $response]);

            // 金額を取得
            $amount = 0;
            if (!empty($response['paymentDetails']['chargeAmount']['amount'])) {
                $amount = (float) $response['paymentDetails']['chargeAmount']['amount'];
            }

            // ✅ AuthorizationId を取得（null 許容）
            $authorizationId = $response['chargePermissionDetails']['authorizationDetails']['authorizationId'] ?? null;

            // === セッションからカート & 住所を取得 ===
            $cart = session('cart', []);
            $address = session('address', []);

            if (empty($cart)) {
                throw new \Exception('カート情報が空です。');
            }

            DB::beginTransaction();

            // === 顧客作成 ===
            $customer = Customer::create([
                'sei'         => $address['order_sei'] ?? 'ゲスト',
                'mei'         => $address['order_mei'] ?? '',
                'email'       => $address['order_email'] ?? ($response['buyer']['email'] ?? 'guest_' . uniqid() . '@example.com'),
                'phone'       => $address['order_phone'] ?? ($response['buyer']['phone'] ?? null),
                'zip'         => $address['order_zip'] ?? null,
                'input_add01' => $address['order_add01'] ?? null,
                'input_add02' => $address['order_add02'] ?? null,
                'input_add03' => $address['order_add03'] ?? null,
            ]);

            // === 配送先作成 ===
            if (($address['same_as_orderer'] ?? '1') === '1') {
                $delivery = Delivery::create($customer->toArray());
            } else {
                $delivery = Delivery::create([
                    'sei'         => $address['delivery_sei'] ?? '',
                    'mei'         => $address['delivery_mei'] ?? '',
                    'email'       => $address['delivery_email'] ?? '',
                    'phone'       => $address['delivery_phone'] ?? '',
                    'zip'         => $address['delivery_zip'] ?? '',
                    'input_add01' => $address['delivery_add01'] ?? '',
                    'input_add02' => $address['delivery_add02'] ?? '',
                    'input_add03' => $address['delivery_add03'] ?? '',
                ]);
            }

            // === 注文作成 ===
            $order = Order::create([
                'order_number'              => Order::generateOrderNumber(),
                'customer_id'               => $customer->id,
                'delivery_id'               => $delivery->id,
                'total_price'               => $amount,
                'delivery_time'             => $address['delivery_time'] ?? null,
                'delivery_date'             => $address['delivery_date'] ?? null,
                'your_request'              => $address['your_request'] ?? null,
                'amazon_checkout_session_id' => $amazonCheckoutSessionId,
                'amazon_charge_id'          => $response['chargeId'] ?? null,
                'authorization_id'          => $authorizationId,
                'amazon_charge_permission_id' => $response['chargePermissionId'] ?? null,
                'status'                    => Order::STATUS_AUTH, // 与信済
            ]);

            // === 注文明細作成 ===
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_code' => $item['product_code'],
                    'name'         => $item['name'],
                    'quantity'     => $item['quantity'],
                    'price'        => $item['price'],
                    'subtotal'     => $item['price'] * $item['quantity'],
                ]);
            }

            DB::commit();

            return [
                'order'    => $order,
                'customer' => $customer,
                'delivery' => $delivery,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('AmazonPay completePayment エラー: ' . $e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }




    /**
     * 決済をキャンセル
     */
    public function cancelPayment($amazonCheckoutSessionId)
    {
        return $this->client->cancelCheckoutSession($amazonCheckoutSessionId);
    }


    public function cancelCharge(string $chargeId): array
    {
        $response = $this->client->cancelCharge(
            $chargeId,
            ['cancellationReason' => 'Order canceled by merchant'],
            [] // options
        );

        return json_decode($response['response']['body'], true);
    }
}
