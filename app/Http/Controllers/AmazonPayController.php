<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Amazon\Pay\API\Client;
use App\Services\AmazonPayService;
use Illuminate\Support\Facades\Log; // ← これを追加



class AmazonPayController extends Controller
{
    protected $amazonPayService;

    public function __construct(AmazonPayService $amazonPayService)
    {
        $this->amazonPayService = $amazonPayService;
    }

    /**
     * 支払いページを表示
     */
    public function showPayment()
    {
        return view('amazonpay.payment');
    }

    /**
     * 決済セッションを作成
     */
    public function createSession(Request $request)
    {
        try {
            $amount = $request->input('amount');
            $paymentData = $this->amazonPayService->createSession($amount);

            $paymentData['amount'] = $amount;
            return view('amazonpay.payment_confirm', $paymentData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '決済の準備中にエラーが発生しました。')->withInput();
        }
    }


    /**
     * 決済完了処理
     */
    public function complete(Request $request)
    {
        $amazonCheckoutSessionId = $request->get('amazonCheckoutSessionId');

        if (empty($amazonCheckoutSessionId)) {
            return redirect()->route('payment.error')->with('error', 'セッションIDが無効です。');
        }

        try {
            // セッションから金額を取得（セキュリティのため）
            $amount = session('payment_amount', '100');
            $result = $this->amazonPayService->completePayment($amazonCheckoutSessionId, $amount);

            return view('amazonpay.complete', [
                'email' => $result['email'],
                'amount' => $amount,
                'orderData' => $result
            ]);
        } catch (\Exception $e) {
            \Log::error('AmazonPay決済エラー: ' . $e->getMessage());
            return redirect()->route('amazon-pay.error')->with('error', '決済処理中にエラーが発生しました。');
        }
    }

    /**
     * 決済キャンセル処理
     */
    public function cancelPayment()
    {
        return view('amazonpay.cancel');
    }

    /**
     * エラーページ
     */
    public function errorPayment()
    {
        return view('amazonpay.error');
    }


    // AmazonPayController.php
    /*
public function webhook(Request $request)
{
    // 生のリクエストボディを取得
    $payload = $request->getContent();
    $headers = $request->headers->all();

    // ログに出力（payloadはJSON、headersは配列）
    \Log::info('AmazonPay Webhook 受信: headers', $headers);
    \Log::info('AmazonPay Webhook 受信: payload', [
        'raw' => $payload,
        'decoded' => json_decode($payload, true),
    ]);

    // いったんOK返す（Amazonに「受信しました」と返さないと再送され続けます）
    return response()->json(['status' => 'ok']);
}

    public function webhook(Request $request)
    {
        // 受け取った内容をログに出す
        Log::info('Amazon Pay Webhook 受信', $request->all());

        // Amazon に 200 を返さないと「通知失敗」になる
        return response()->json(['status' => 'ok']);
    }


    public function webhook(Request $request)
    {
        $data = $request->all();
        \Log::info('Amazon Pay Webhook 受信', $data);

        $type = $data['notificationType'] ?? null;

        switch ($type) {
            case 'ChargePermissionStateChange':
                // 与信許可・キャンセル時の処理
                break;

            case 'ChargeStateChange':
                // 与信確定 / 売上確定の処理
                break;

            case 'RefundStateChange':
                // 返金処理
                break;

            default:
                \Log::warning('未知のWebhook通知', $data);
        }

        return response()->json(['status' => 'ok']);
    }
*/

public function webhook(Request $request)
{
    $rawBody = $request->getContent();
    $payload = json_decode($rawBody, true);

    // ログ：外側のデータ
    Log::info('Amazon Pay Webhook 外側: ' . json_encode($payload, JSON_UNESCAPED_UNICODE));

    if (isset($payload['Message'])) {
        $innerMessage = json_decode($payload['Message'], true);
        Log::info('Amazon Pay Webhook 内側: ' . json_encode($innerMessage, JSON_UNESCAPED_UNICODE));
    } else {
        Log::warning('Amazon Pay Webhook: Message フィールドが見つかりません');
    }

    return response()->json(['status' => 'ok']);
}

}
