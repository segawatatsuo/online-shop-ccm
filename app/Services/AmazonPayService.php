<?php

namespace App\Services;

use Amazon\Pay\API\Client;
use Amazon\Pay\API\Constants\Environment;

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

    /**
     * 決済をキャンセル
     */
    public function cancelPayment($amazonCheckoutSessionId)
    {
        return $this->client->cancelCheckoutSession($amazonCheckoutSessionId);
    }
}