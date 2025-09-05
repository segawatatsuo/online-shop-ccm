<?php

namespace App\Services;

use Amazon\Pay\API\Client;
use Amazon\Pay\API\Constants\Environment;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;

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
    public function captureCharge(string $chargeId, int $amount): array
    {
        $response = $this->client->captureCharge(
            $chargeId,
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
                'checkoutResultReturnUrl' => route('amazon-pay.complete'),
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







    /**
     * 決済を完了
     */

    /*
    public function completePayment($amazonCheckoutSessionId, $amount)
    {
        // 注文情報を取得
        $result = $this->client->getCheckoutSession($amazonCheckoutSessionId);

        $response = json_decode($result['response'], true);

        // 購入者のemailアドレスを確認
        $email = $response['buyer']['email'] ?? null;
        if (empty($email)) {
            throw new \Exception('購入者のメールアドレスが取得できませんでした。');
        }

        // 売上請求処理
        $payload = [
            'chargeAmount' => [
                'amount' => (string)$amount,
                'currencyCode' => 'JPY',
            ],
        ];
        
        $checkoutResponse = $this->client->completeCheckoutSession($amazonCheckoutSessionId, $payload);

        if ($checkoutResponse['status'] !== 200) {
            throw new \Exception('決済の完了処理に失敗しました。');
        }

        return [
            'email' => $email,
            'checkoutSessionId' => $amazonCheckoutSessionId,
            'response' => $response,
        ];
    }
    */

public function completePayment(string $amazonCheckoutSessionId, float $amount): array
{
    \Log::info('AmazonPay completePayment() 開始', ['amazonCheckoutSessionId' => $amazonCheckoutSessionId]);

    try {
        // ✅ Idempotency Key を必ず設定
        $idempotencyKey = uniqid('amazonpay_', true);

        $response = $this->client->completeCheckoutSession(
            $amazonCheckoutSessionId,
            [
                'headers' => [
                    'x-amz-pay-idempotency-key' => $idempotencyKey,
                ],
            ]
        );

        \Log::info('AmazonPay completePayment() 結果', ['raw' => $response]);

        // API から顧客情報を取得（存在しない場合は仮データ）
        $buyer = $response['buyer'] ?? [];

        $email = $buyer['email'] ?? 'guest_' . uniqid() . '@example.com';
        $firstName = $buyer['name']['firstName'] ?? '';
        $lastName = $buyer['name']['lastName'] ?? 'ゲスト';
        $phone = $buyer['phone'] ?? null;
        $zip = $buyer['address']['postalCode'] ?? null;
        $add1 = $buyer['address']['addressLine1'] ?? null;
        $add2 = $buyer['address']['addressLine2'] ?? null;
        $city = $buyer['address']['city'] ?? null;

        // ✅ Customer 作成（ゲスト対応）
        $customer = Customer::create([
            'sei'         => $lastName,
            'mei'         => $firstName,
            'email'       => $email,
            'phone'       => $phone,
            'zip'         => $zip,
            'input_add01' => $add1,
            'input_add02' => $add2,
            'input_add03' => $city,
        ]);

        // ✅ Order 作成
        $order = new \App\Models\Order();
        $order->amazon_checkout_session_id = $amazonCheckoutSessionId;
        $order->amazon_charge_id           = $response['chargeId'] ?? null;
        $order->order_number               = uniqid('order_');
        $order->customer_id                = $customer->id;
        $order->total_price                = $amount;
        $order->status                     = \App\Models\Order::STATUS_AUTH; // 与信済
        $order->save();

        return [
            'email'    => $email,
            'chargeId' => $response['chargeId'] ?? null,
            'status'   => $response['status'] ?? null,
        ];

    } catch (\Exception $e) {
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
