<?php

namespace App\Services;

use Amazon\Pay\API\Client;
use App\Models\Order;
use Illuminate\Support\Str;

class AmazonPayService
{
    protected Client $client;

    public function __construct()
    {
        $config = [
            'public_key_id' => config('amazonpay.public_key_id'),
            'private_key' => config('amazonpay.private_key_path'),
            'region' => config('amazonpay.region'),
            'sandbox' => config('amazonpay.sandbox'),
        ];

        $this->client = new Client($config);
    }

    /**
     * Checkout Session 作成
     */
    public function createPaymentSession(float $amount, ?string $merchantReferenceId = null): array
    {
        $merchantReferenceId = $merchantReferenceId ?: 'order_' . time();

        // セッションに金額を保存（安全のため）
        session(['payment_amount' => $amount]);

        $payload = [
            'webCheckoutDetails' => [
                'checkoutResultReturnUrl' => route('amazon-pay.complete'),
                'checkoutCancelUrl' => route('amazon-pay.cancel'),
                'checkoutReviewReturnUrl' => route('amazon-pay.complete'),
            ],
            'storeId' => config('amazonpay.store_id'),
            'paymentDetails' => [
                'paymentIntent' => 'AuthorizeWithCapture', // AuthorizeWithCaptureに戻す
                'chargeAmount' => [
                    'amount' => (string)$amount,
                    'currencyCode' => 'JPY',
                ],
                'canHandlePendingAuthorization' => false, // 保留中の認証を処理しない
            ],
            'merchantMetadata' => [
                'merchantReferenceId' => $merchantReferenceId,
                'merchantStoreName' => config('amazonpay.store_name'),
                'noteToBuyer' => '料金のお支払いです',
            ],
            'scopes' => ['name', 'email'],
            // 重要：Charge Permissionを作成するために必要
            'chargePermissionType' => 'OneTime',
        ];

        // 一意の idempotency key を生成
        $idempotencyKey = (string) Str::uuid();

        $response = $this->client->createCheckoutSession(
            $payload,
            ['x-amz-pay-idempotency-key' => $idempotencyKey]
        );

        $result = json_decode($response['response'], true);

        if (empty($result['checkoutSessionId'])) {
            throw new \Exception('CheckoutSession の取得に失敗しました。');
        }

        // 署名付きデータを返却（Amazon Pay ボタン表示用）
        $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $signature = $this->client->generateButtonSignature($payloadJson);

        return [
            'checkoutSessionId' => $result['checkoutSessionId'],
            'payloadJson'       => $payloadJson,
            'signature'         => $signature,
            'publicKeyId'       => config('amazonpay.public_key_id'),
            'merchantId'        => config('amazonpay.merchant_id'),
            'sandbox'           => config('amazonpay.sandbox'),
        ];
    }

    /**
     * Checkout Session 完了（売上確定）
     */
    public function completePayment(string $checkoutSessionId, float $amount): array
    {
        // まずセッションの詳細を取得
        $result = $this->client->getCheckoutSession($checkoutSessionId);

        if ($result['status'] !== 200) {
            throw new \Exception('Checkout Session の取得に失敗しました。');
        }

        $response = json_decode($result['response'], true);

        $email = $response['buyer']['email'] ?? null;
        if (empty($email)) {
            throw new \Exception('購入者メールアドレスが取得できません。');
        }

        $sessionStatus = $response['statusDetails']['state'] ?? 'unknown';
        $paymentIntent = $response['paymentDetails']['paymentIntent'] ?? null;

        \Log::info('Checkout session initial state:', [
            'sessionId' => $checkoutSessionId,
            'state' => $sessionStatus,
            'paymentIntent' => $paymentIntent,
            'buyer_email' => $email,
            'chargeId' => $response['chargeId'] ?? 'NOT_FOUND',
            'chargePermissionId' => $response['chargePermissionId'] ?? 'NOT_FOUND',
            'response_keys' => array_keys($response)
        ]);

        switch ($sessionStatus) {
            case 'Completed':
                // 既に完了済みの場合
                return $this->handleCompletedSession($checkoutSessionId, $response);

            case 'Open':
                // Open状態の場合の処理
                return $this->handleOpenSession($checkoutSessionId, $amount, $email, $response, $paymentIntent);

            case 'Canceled':
                throw new \Exception('Amazon Pay によりキャンセルされました。');

            case 'Declined':
                throw new \Exception('Amazon Pay により決済が拒否されました。');

            default:
                throw new \Exception("予期しないセッション状態です: {$sessionStatus}");
        }
    }

    /**
     * 完了済みセッションの処理
     */
    private function handleCompletedSession(string $checkoutSessionId, array $response): array
    {
        $chargeId = $response['chargeId'] ?? null;
        $chargePermissionId = $response['chargePermissionId'] ?? null;

        if ($chargeId) {
            // Chargeの詳細を確認
            $chargeResult = $this->client->getCharge($chargeId);
            $chargeResponse = json_decode($chargeResult['response'], true);
            $chargeStatus = $chargeResponse['statusDetails']['state'] ?? 'unknown';

            \Log::info('Charge status check:', [
                'chargeId' => $chargeId,
                'status' => $chargeStatus
            ]);

            if ($chargeStatus === 'Captured' || $chargeStatus === 'Authorized') {
                return [
                    'email' => $response['buyer']['email'],
                    'checkoutSessionId' => $checkoutSessionId,
                    'chargeId' => $chargeId,
                    'chargePermissionId' => $chargePermissionId,
                    'chargeStatus' => $chargeStatus,
                    'response' => $response,
                ];
            }
        }

        return [
            'email' => $response['buyer']['email'],
            'checkoutSessionId' => $checkoutSessionId,
            'chargePermissionId' => $chargePermissionId,
            'response' => $response,
        ];
    }

    /**
     * Open 状態のセッションに対する処理
     */
    private function handleOpenSession(string $checkoutSessionId, float $amount, string $email, array $response, ?string $paymentIntent): array
    {
        \Log::info('HandleOpenSession started:', [
            'sessionId' => $checkoutSessionId,
            'paymentIntent' => $paymentIntent,
            'email' => $email,
            'response_keys' => array_keys($response),
            'chargePermissionId' => $response['chargePermissionId'] ?? 'NOT_FOUND',
            'chargeId' => $response['chargeId'] ?? 'NOT_FOUND'
        ]);

        // AuthorizeWithCaptureの場合、ユーザーがAmazon Payで決済を完了した後、
        // セッションは自動的に処理されるので、単に決済完了を待つ
        if ($paymentIntent === 'AuthorizeWithCapture') {
            return $this->handleAuthorizeWithCaptureSession($checkoutSessionId, $amount, $email, $response);
        }

        // それ以外のpaymentIntentの場合の処理
        try {
            // まず、updateCheckoutSessionで決済情報を確定させる
            $updatePayload = [
                'paymentDetails' => [
                    'chargeAmount' => [
                        'amount' => (string)$amount,
                        'currencyCode' => 'JPY',
                    ],
                ],
                'merchantMetadata' => [
                    'merchantReferenceId' => 'order_' . time(),
                    'merchantStoreName' => config('amazonpay.store_name'),
                    'noteToBuyer' => '料金のお支払いです',
                ],
            ];

            \Log::info('Updating checkout session:', [
                'sessionId' => $checkoutSessionId,
                'updatePayload' => $updatePayload
            ]);

            $updateResponse = $this->client->updateCheckoutSession($checkoutSessionId, $updatePayload);

            if ($updateResponse['status'] !== 200) {
                $errorResponse = json_decode($updateResponse['response'] ?? '{}', true);
                throw new \Exception('Checkout Session の更新に失敗しました: ' . ($errorResponse['message'] ?? 'Unknown error'));
            }

            // 更新後のセッション情報を取得
            $updatedResult = $this->client->getCheckoutSession($checkoutSessionId);
            $updatedResponse = json_decode($updatedResult['response'], true);

            $chargePermissionId = $updatedResponse['chargePermissionId'] ?? null;

            if (!$chargePermissionId) {
                throw new \Exception('決済処理の準備ができていません。時間をおいて再度お試しください。');
            }

            // Charge Permissionが取得できた場合、決済を実行
            return $this->processChargeWithPermission($checkoutSessionId, $chargePermissionId, $amount, $email, $updatedResponse);

        } catch (\Exception $e) {
            \Log::error('Failed to handle open session:', [
                'sessionId' => $checkoutSessionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('決済を完了できませんでした: ' . $e->getMessage());
        }
    }

    /**
     * AuthorizeWithCaptureタイプのセッション処理
     */
    private function handleAuthorizeWithCaptureSession(string $checkoutSessionId, float $amount, string $email, array $response): array
    {
        // AuthorizeWithCaptureの場合、ユーザーがAmazon Pay側で決済を完了すると
        // 自動的にChargeが作成され、セッション状態がCompletedに変更される
        
        // 既にChargeIdが存在する場合（決済完了済み）
        $chargeId = $response['chargeId'] ?? null;
        $chargePermissionId = $response['chargePermissionId'] ?? null;

        if ($chargeId) {
            \Log::info('Charge already exists for AuthorizeWithCapture session:', [
                'sessionId' => $checkoutSessionId,
                'chargeId' => $chargeId,
                'chargePermissionId' => $chargePermissionId
            ]);

            return [
                'email' => $email,
                'checkoutSessionId' => $checkoutSessionId,
                'chargeId' => $chargeId,
                'chargePermissionId' => $chargePermissionId,
                'response' => $response,
            ];
        }

        // Charge Permissionが存在する場合、手動でChargeを作成
        if ($chargePermissionId) {
            \Log::info('Creating manual charge for AuthorizeWithCapture:', [
                'sessionId' => $checkoutSessionId,
                'chargePermissionId' => $chargePermissionId
            ]);

            return $this->processChargeWithPermission($checkoutSessionId, $chargePermissionId, $amount, $email, $response);
        }

        // セッション情報を少し待ってから再取得
        \Log::info('Waiting for session to be processed by Amazon Pay:', [
            'sessionId' => $checkoutSessionId
        ]);

        // 少し待機してから再取得
        sleep(2);

        $result = $this->client->getCheckoutSession($checkoutSessionId);
        if ($result['status'] !== 200) {
            throw new \Exception('Checkout Session の再取得に失敗しました。');
        }

        $updatedResponse = json_decode($result['response'], true);
        $sessionStatus = $updatedResponse['statusDetails']['state'] ?? 'unknown';

        \Log::info('Updated session status after wait:', [
            'sessionId' => $checkoutSessionId,
            'state' => $sessionStatus,
            'chargeId' => $updatedResponse['chargeId'] ?? 'NOT_FOUND',
            'chargePermissionId' => $updatedResponse['chargePermissionId'] ?? 'NOT_FOUND'
        ]);

        if ($sessionStatus === 'Completed') {
            return $this->handleCompletedSession($checkoutSessionId, $updatedResponse);
        }

        $newChargeId = $updatedResponse['chargeId'] ?? null;
        $newChargePermissionId = $updatedResponse['chargePermissionId'] ?? null;

        if ($newChargeId) {
            return [
                'email' => $email,
                'checkoutSessionId' => $checkoutSessionId,
                'chargeId' => $newChargeId,
                'chargePermissionId' => $newChargePermissionId,
                'response' => $updatedResponse,
            ];
        }

        if ($newChargePermissionId) {
            return $this->processChargeWithPermission($checkoutSessionId, $newChargePermissionId, $amount, $email, $updatedResponse);
        }

        throw new \Exception('Amazon Payでの決済処理が完了していません。しばらく待ってから再度お試しください。');
    }

    /**
     * Checkout Session キャンセル
     */
    public function cancelPayment(string $checkoutSessionId): array
    {
        return $this->client->cancelCheckoutSession($checkoutSessionId);
    }

    /**
     * SDK Client を取得
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}