<?php
// app/Services/AmazonPayService.php
namespace App\Services;

use Amazon\Pay\API\Client;
use Exception;
use Illuminate\Support\Str;
use Log;

class AmazonPayService
{
    protected Client $client;

    public function __construct()
    {
        $config = config('amazonpay');

        $this->client = new Client([
            'public_key_id' => $config['public_key_id'],
            'private_key' => file_get_contents($config['private_key']),
            'region' => $config['region'],
            'sandbox' => $config['sandbox'],
            'store_id' => $config['store_id'],
        ]);
    }

    public function createCheckoutSession(string $webCheckoutUrl): array
    {
        $payload = [
            'webCheckoutDetails' => [
                'checkoutReviewReturnUrl' => $webCheckoutUrl, //購入者がAmazon Payのレビュー画面を完了した後に戻ってくるべきURL
                'checkoutResultReturnUrl' => $webCheckoutUrl, //購入者が決済結果画面（成功・失敗など）を見た後に戻ってくるべきURL
            ],
            'storeId' => config('amazonpay.store_id'), //Laravelのヘルパー関数で、config/amazonpay.phpから 'store_id' の値を取得
            'paymentDetails' => [
                'paymentIntent' => 'Authorize', // 決済の種類.標準的な認証のみの 'Authorize' を指定
                'chargeAmount' => [
                    'amount' => '100',
                    'currencyCode' => 'JPY'
                ],
            ],
            'merchantMetadata' => [
                'merchantReferenceId' => uniqid('order_'),
                'merchantStoreName' => 'テストストア',
            ],
        ];

        $idempotencyKey = (string) Str::uuid(); // 一意のID生成
        $headers = [
            'x-amz-pay-idempotency-key' => $idempotencyKey,
        ];

        // Checkout セッションを作成
        $response = $this->client->createCheckoutSession($payload, $headers);
        dd($response);
        $data = json_decode($response['response'], true);

        // API応答が成功しているか、checkoutSessionId が存在するかを確認
        if (!isset($data['checkoutSessionId'])) {
            // checkoutSessionId が存在しない場合はエラー処理を行う
            // 応答全体やエラーメッセージをログに出力するなど
            \Log::error('Amazon Pay createCheckoutSession API error', [
                'response_status' => $response['status'] ?? 'N/A',
                'response_body' => $data, // エラーの詳細が含まれている可能性がある
                'payload' => $payload,
            ]);

            // 例外をスローするか、エラーを示す値を返す
            // 今回は例外をスローする例
            throw new Exception('Failed to create Amazon Pay checkout session. Error: ' . json_encode($data));
        }

        // チェックアウトセッションIDの取得
        $sessionId = $data['checkoutSessionId'];

        // セッション詳細を取得
        $detailsResponse = $this->client->getCheckoutSession($sessionId);
        $details = json_decode($detailsResponse['response'], true);

        // 詳細の確認
        dd($details); // 全ての詳細を表示して、'amazonPayRedirectUrl' を確認

        // 取得した情報を返却
        return $details;
    }
}
