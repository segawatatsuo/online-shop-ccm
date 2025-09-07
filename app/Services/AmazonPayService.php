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
    $orderId = $merchantReferenceId ?: 'ORD' . date('YmdHis');

    // セッションに金額を保存（セキュリティのため）
    session(['payment_amount' => $amount]);

$payload = [
    'webCheckoutDetails' => [
        'checkoutReviewReturnUrl' => route('amazon-pay.review'), // ← 必須
        'checkoutResultReturnUrl' => route('amazon-pay.complete') . '?amazonCheckoutSessionId={checkoutSessionId}',
        'checkoutCancelUrl'       => route('amazon-pay.cancel'),
    ],
    'storeId'              => config('services.amazonpay.store_id'),
    'chargePermissionType' => 'OneTime',
    'merchantMetadata'     => [
        'merchantReferenceId' => $orderId, // 例: 自社注文番号
        'merchantStoreName'   => 'SHOP_NAME',
        'noteToBuyer'         => '料金のお支払いです',
    ],
    'paymentDetails'       => [
        'paymentIntent' => 'AuthorizeWithCapture', // 与信+売上確定
        'chargeAmount'  => [
            'amount'       => (string)$amount,
            'currencyCode' => 'JPY',
        ],
    ],
];

    /*
    $headers = [
        'x-amz-pay-idempotency-key' => uniqid('amazonpay_', true),
    ];
    */

$headers = [
    // uniqid() の代わりに bin2hex(random_bytes()) を使用
    'x-amz-pay-idempotency-key' => 'amazonpay_' . bin2hex(random_bytes(8)),
];


    // ✅ checkoutMode は削除！
    $response = $this->client->createCheckoutSession($payload, $headers);

    if (!isset($response['checkoutSessionId'])) {
        throw new \Exception('Amazon Pay から checkoutSessionId が返ってきませんでした: ' . json_encode($response));
    }

    return [
        'checkoutSessionId'  => $response['checkoutSessionId'],
        'webCheckoutDetails' => $response['webCheckoutDetails'] ?? null,
    ];
}






public function createPaymentSession(float $amount, ?string $merchantReferenceId = null): array
{
    // merchantReferenceId（自社注文番号）を用意
    $merchantReferenceId = $merchantReferenceId ?: 'ORD' . now()->format('YmdHis');

    // JPYなら小数は不要。整数文字列で渡す（Amazon API 想定）
    $amountStr = (string) intval(round($amount));

    $payload = [
        'webCheckoutDetails' => [
            // Amazon が {checkoutSessionId} を置換する想定の URL
            'checkoutResultReturnUrl' => route('amazon-pay.complete') . '?amazonCheckoutSessionId={checkoutSessionId}',
            // 購入確認ページ（Amazon → 戻る先）を指定（要ルート or とりあえず complete にしても可）
            'checkoutReviewReturnUrl' => route('amazon-pay.complete'),
            'checkoutCancelUrl'       => route('amazon-pay.cancel'),
        ],
        // config のキー名はあなたの設定に合わせてください
        'storeId' => config('amazonpay.store_id'), // 例: config('amazonpay.store_id')
        'chargePermissionType' => 'OneTime',
        'merchantMetadata' => [
            'merchantReferenceId' => $merchantReferenceId,
            'merchantStoreName'   => config('amazonpay.store_name') ?? 'SHOP_NAME',
            'noteToBuyer'         => '料金のお支払いです',
        ],
        'paymentDetails' => [
            'paymentIntent' => 'AuthorizeWithCapture',
            'chargeAmount'  => [
                'amount'       => $amountStr,
                'currencyCode' => 'JPY',
            ],
        ],
    ];

    // idempotency key（許容文字だけを使う）
    $headers = [
        'x-amz-pay-idempotency-key' => 'amazonpay_' . bin2hex(random_bytes(8)), // <= 英数字のみ
    ];

    // SDK のメソッドは (payload, headers)
    $response = $this->client->createCheckoutSession($payload, $headers);

    // SDK の返り値の構造に合わせて body を取り出す（ログで確認した形に基づく）
    $body = json_decode($response['response'] ?? '{}', true);

    if (empty($body['checkoutSessionId'])) {
        // 詳細ログを残しつつ例外を投げます（デバッグ用）
        throw new \Exception('Amazon Pay から checkoutSessionId が返ってきませんでした: ' . json_encode($body));
    }

    return [
        'checkoutSessionId' => $body['checkoutSessionId'],
        // Amazon のレスポンスに webCheckoutDetails.webCheckoutUrl が含まれる想定
        'webCheckoutUrl'    => $body['webCheckoutDetails']['webCheckoutUrl'] ?? null,
        'raw'               => $body,
    ];
}




public function completePayment(string $amazonCheckoutSessionId): array
{
    \Log::info('AmazonPay completePayment() 開始', [
        'amazonCheckoutSessionId' => $amazonCheckoutSessionId
    ]);

    // 仮注文を取得
    $order = Order::where('amazon_checkout_session_id', $amazonCheckoutSessionId)->firstOrFail();

    $amount = $order->amount; // 仮注文の金額

    $idempotencyKey = uniqid('amazonpay_', true);

    // headers は第3引数で渡す
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

    // AuthorizationId を取得（null 許容）
    $authorizationId = $response['chargePermissionDetails']['authorizationDetails']['authorizationId'] ?? null;

    DB::beginTransaction();
    try {
        // 顧客作成
        $customer = Customer::create([
            'sei'         => $order->order_sei ?? 'ゲスト',
            'mei'         => $order->order_mei ?? '',
            'email'       => $order->order_email ?? ($response['buyer']['email'] ?? 'guest_' . uniqid() . '@example.com'),
            'phone'       => $order->order_phone ?? ($response['buyer']['phone'] ?? null),
            'zip'         => $order->order_zip ?? null,
        ]);

        // 配送先作成（必要なら）
        $delivery = Delivery::create($customer->toArray());

        // 注文更新
        $order->update([
            'customer_id'               => $customer->id,
            'delivery_id'               => $delivery->id,
            'total_price'               => $amount,
            'authorization_id'          => $authorizationId,
            'amazon_charge_id'          => $response['chargeId'] ?? null,
            'amazon_charge_permission_id' => $response['chargePermissionId'] ?? null,
            'status'                    => Order::STATUS_AUTH, // 与信済
        ]);

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

// app/Services/AmazonPayService.php

public function getCheckoutSession(string $checkoutSessionId): array
{
    $response = $this->client->getCheckoutSession($checkoutSessionId);

    $body = $response['response'] ?? null;
    if (!$body) {
        throw new \Exception('Amazon Pay から CheckoutSession の情報を取得できませんでした。');
    }

    return json_decode($body, true);
}


}
