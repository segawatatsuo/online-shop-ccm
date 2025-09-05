<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Amazon\Pay\API\Client;
use App\Services\AmazonPayService;
use Illuminate\Support\Facades\Log; // ← これを追加
use App\Models\Order;


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


    // CheckoutSession 作成時（仮注文保存）
public function createPaymentSession(Request $request)
{
    $amount = $request->input('amount');
    $orderNumber = 'ORD' . now()->format('YmdHis');

    // CheckoutSession 作成
    $result = $this->amazonPayService->createPaymentSession($amount, $orderNumber);

    // 仮注文を作成
    $order = Order::create([
        'order_number' => $orderNumber,
        'amount' => $amount,
        'status' => 'pending', // 仮注文
        'amazon_checkout_session_id' => $result['checkoutSessionId'],
    ]);

    return redirect($result['webCheckoutUrl']);
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
        $amount = session('payment_amount', 100);

        $result = $this->amazonPayService->completePayment($amazonCheckoutSessionId, $amount);

        // 仮注文取得 or 新規作成済みの Order を探す
        $order = Order::where('amazon_checkout_session_id', $amazonCheckoutSessionId)->first();

        if ($order) {
            $order->amazon_charge_id = $result['chargeId'];
            $order->status = Order::STATUS_AUTH; // 与信済みに更新
            $order->save();
        }

        return view('amazonpay.complete', [
            'email' => $result['email'],
            'amount' => $amount,
            'orderData' => $result,
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
/*
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
        */



public function webhook(Request $request)
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['Message'])) {
        Log::warning('Amazon Pay Webhook: Messageなし', $data);
        return response()->json(['status' => 'ignored']);
    }

    $message = json_decode($data['Message'], true);

    Log::info('Amazon Pay Webhook 内側', $message);

    // CHARGE に関する通知だけ処理
    if (($message['ObjectType'] ?? null) === 'CHARGE') {
        $order = Order::where('amazon_charge_permission_id', $message['ChargePermissionId'])->first();

        if ($order) {
            $order->amazon_charge_id = $message['ObjectId'] ?? null;
            $order->payment_status   = $message['NotificationType'] ?? 'Unknown';
            $order->save();

            Log::info("注文 {$order->id} を更新しました", [
                'status' => $order->payment_status,
            ]);
        } else {
            Log::warning('対応する注文が見つかりません', $message);
        }
    }

    return response()->json(['status' => 'ok']);
}





}
