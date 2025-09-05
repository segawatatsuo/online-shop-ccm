<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Services\AmazonPayService;
use Illuminate\Support\Facades\Log;


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
     * チェックアウト完了（与信）処理
     */
    public function complete(Request $request)
    {
        $amazonCheckoutSessionId = $request->get('amazonCheckoutSessionId');
        Log::info('AmazonPay complete() 開始', ['amazonCheckoutSessionId' => $amazonCheckoutSessionId]);

        if (empty($amazonCheckoutSessionId)) {
            return redirect()->route('payment.error')
                ->with('error', 'セッションIDが無効です。');
        }

        try {
            // セッションから金額取得
            $amountSession = session('payment_amount', 0);

            // Amazon Pay 完了処理（与信）
            $result = $this->amazonPayService->completePayment($amazonCheckoutSessionId, $amountSession);
            Log::info('AmazonPay completePayment() 結果', $result);

            $amountFromAmazon = $result['amount'] ?? $amountSession;

            // 金額差異チェック
            if ($amountFromAmazon != $amountSession) {
                Log::warning('決済金額差異', [
                    'session' => $amountSession,
                    'amazon'  => $amountFromAmazon
                ]);
                return redirect()->route('payment.error')
                    ->with('error', '決済金額が一致しません。');
            }

            // =========================
            // Customer作成（ゲスト対応）
            // =========================
            if (auth()->check()) {
                $customer = auth()->user()->customer;
            } else {
                $buyer = $result['buyer'] ?? [];
                $customer = Customer::create([
                    'sei'         => $buyer['name']['lastName'] ?? 'ゲスト',
                    'mei'         => $buyer['name']['firstName'] ?? '',
                    'email'       => $buyer['email'] ?? null,
                    'phone'       => $buyer['phone'] ?? null,
                    'zip'         => $buyer['address']['postalCode'] ?? null,
                    'input_add01' => $buyer['address']['addressLine1'] ?? null,
                    'input_add02' => $buyer['address']['addressLine2'] ?? null,
                    'input_add03' => $buyer['address']['city'] ?? null,
                ]);
            }

            // =========================
            // Order作成
            // =========================
            $order = new Order();
            $order->amazon_checkout_session_id = $amazonCheckoutSessionId;
            $order->amazon_charge_id           = $result['chargeId'] ?? null;
            $order->order_number               = uniqid('order_');
            $order->customer_id                = $customer->id;
            $order->total_price                = $amountSession;
            $order->status                     = Order::STATUS_AUTH; // 与信済
            $order->save();

            return view('amazonpay.complete', [
                'email'     => $result['email'] ?? null,
                'amount'    => $amountSession,
                'orderData' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('AmazonPay決済エラー: ' . $e->getMessage());
            return redirect()->route('amazon-pay.error')
                ->with('error', '決済処理中にエラーが発生しました。');
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


    /**
     * Webhook受信処理（STATE_CHANGE）
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Amazon Pay Webhook 受信', $payload);

        $objectType = $payload['ObjectType'] ?? null;
        $objectId   = $payload['ObjectId'] ?? null;
        $chargeId   = $payload['ChargePermissionId'] ?? null;

        try {
            if ($objectType === 'CHARGE' || $objectType === 'CHARGE_PERMISSION') {
                $order = Order::where('amazon_checkout_session_id', $objectId)
                    ->orWhere('amazon_charge_id', $chargeId)
                    ->first();

                if (!$order) {
                    Log::warning('対応する注文が見つかりません', $payload);
                    return response()->json(['status' => 'not_found'], 404);
                }

                // STATE_CHANGEに応じて注文ステータスを更新
                $notificationType = $payload['NotificationType'] ?? '';
                $newState = $payload['NewState'] ?? '';

                if ($notificationType === 'STATE_CHANGE') {
                    switch ($newState) {
                        case 'CHARGE_CAPTURED':
                            $order->status = Order::STATUS_PAID; // 売上確定
                            $order->save();
                            Log::info('注文売上確定', ['order_id' => $order->id]);
                            break;
                        case 'CHARGE_DECLINED':
                            $order->status = Order::STATUS_DECLINED;
                            $order->save();
                            Log::warning('注文与信失敗', ['order_id' => $order->id]);
                            break;
                        // 他のステータスも必要に応じて追加
                    }
                }
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Webhook処理エラー: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }




}
