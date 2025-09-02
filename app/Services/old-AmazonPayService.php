<?php

namespace App\Services;

use Amazon\Pay\API\Client;
use Amazon\Pay\API\Constants\Environment;
use App\Models\Order;
use App\Mail\OrderConfirmed;
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
     * 決済セッションを作成
     */
 
    public function createPaymentSession($amount, $merchantReferenceId = null)
    {
        $merchantReferenceId = $merchantReferenceId ?: 'Order_' . time();
        //dd($merchantReferenceId); "Order_1756473187"
        
        // セッションに金額を保存（セキュリティのため）
        session(['payment_amount' => $amount]);
        
        $payload = [
            'webCheckoutDetails' => [
                'checkoutResultReturnUrl' => route('amazon-pay.complete'),//Amazon が checkoutSessionId を持った状態で あなたのサイトにリダイレクトします。
                'checkoutCancelUrl' => route('amazon-pay.cancel'),//ユーザーがAmazonの画面で「キャンセル」した場合に戻される先。
                'checkoutMode' => 'ProcessOrder',//承認→即時売上フロー（通常はこちら）
            ],
            'storeId' => config('amazonpay.store_id'),
            'chargePermissionType' => 'OneTime',
            'merchantMetadata' => [
                'merchantReferenceId' => $merchantReferenceId,
                'merchantStoreName' => config('amazon-pay.store_name'),
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


    // Amazon Pay API で Checkout Session を作成
    $response = $this->client->createCheckoutSession($payload,[]);
    $result = json_decode($response['response'], true);

    if (empty($result['checkoutSessionId'])) {
        throw new \Exception('Amazon Checkout Session ID の取得に失敗しました');
    }

    // ここで orders テーブルに checkoutSessionId を保存
    Order::where('id', $orderId)->update([
        'amazon_checkout_session_id' => $result['checkoutSessionId'],
    ]);



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


/*
public function createPaymentSession($orderId, $amount)
{
    // 注文番号を merchantReferenceId にして Amazon 側に伝える
    $merchantReferenceId = 'Order_' . $orderId;

    // セッションに金額を保存（セキュリティのため）
    session(['payment_amount' => $amount]);

    $payload = [
        'webCheckoutDetails' => [
            'checkoutResultReturnUrl' => route('amazon-pay.complete'), // 決済完了後のリダイレクト
            'checkoutCancelUrl' => route('amazon-pay.cancel'),         // キャンセル時のリダイレクト
            'checkoutMode' => 'ProcessOrder',
        ],
        'storeId' => config('amazonpay.store_id'),
        'chargePermissionType' => 'OneTime',
        'merchantMetadata' => [
            'merchantReferenceId' => $merchantReferenceId,
            'merchantStoreName' => config('amazon-pay.store_name'),
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

    // Amazon Pay API で Checkout Session を作成
    $response = $this->client->createCheckoutSession($payload);
    $result = json_decode($response['response'], true);

    if (empty($result['checkoutSessionId'])) {
        throw new \Exception('Amazon Checkout Session ID の取得に失敗しました');
    }

    // ここで orders テーブルに checkoutSessionId を保存
    Order::where('id', $orderId)->update([
        'amazon_checkout_session_id' => $result['checkoutSessionId'],
    ]);

    // 署名付きデータを返却（ボタン表示用）
    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
    $signature = $this->client->generateButtonSignature($payloadJson);

    return [
        'payloadJson' => $payloadJson,
        'signature' => $signature,
        'publicKeyId' => config('amazonpay.public_key_id'),
        'merchantId' => config('amazonpay.merchant_id'),
        'sandbox' => config('amazonpay.sandbox'),
        'checkoutSessionId' => $result['checkoutSessionId'],
    ];
}
*/



    /**
     * 決済を完了
     */
public function completePayment($amazonCheckoutSessionId, $amount)
{
    // 1. Amazonセッションを取得
    $result = $this->client->getCheckoutSession($amazonCheckoutSessionId);
    $response = json_decode($result['response'], true);

    // 2. 購入者情報チェック
    $email = $response['buyer']['email'] ?? null;
    if (empty($email)) {
        throw new \Exception('購入者のメールアドレスが取得できませんでした。');
    }

    // 3. 売上請求処理
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

    // 4. 完了結果を返す
    return [
        'email' => $email,
        'checkoutSessionId' => $amazonCheckoutSessionId,
        'response' => $response,
    ];
}


    /**
     * 決済をキャンセル
     */
    public function cancelPayment($amazonCheckoutSessionId)
    {
        return $this->client->cancelCheckoutSession($amazonCheckoutSessionId);
    }
}