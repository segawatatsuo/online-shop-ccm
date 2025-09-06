<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Delivery;
use App\Models\Customer;
use Illuminate\Support\Facades\Session;
use App\Services\AmazonPayService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderThanksMail;
use App\Mail\OrderConfirmed;
use App\Mail\OrderNotification;
use App\Models\DeliveryTime; // 追加
use App\Models\ShippingFee;
use Illuminate\Support\Facades\DB;


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
    $amazonCheckoutSessionId = $request->query('amazonCheckoutSessionId');
    \Log::info('AmazonPay complete() 開始', [
        'amazonCheckoutSessionId' => $amazonCheckoutSessionId
    ]);

    try {
        $result = $this->amazonPayService->completePayment($amazonCheckoutSessionId);

        $order    = $result['order'];
        $customer = $result['customer'];
        $delivery = $result['delivery'];

        // === メール送信 ===
        try {
            Mail::to($customer->email)->send(new OrderConfirmed($order, $customer, $delivery));
            \Log::info('顧客向け注文確認メール送信完了', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            \Log::error('顧客向け注文確認メール送信失敗', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        try {
            Mail::to('segawa82@nifty.com')->send(new OrderNotification($order, $customer, $delivery));
            \Log::info('ショップ向け注文通知メール送信完了', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            \Log::error('ショップ向け注文通知メール送信失敗', ['order_id' => $order->id, 'error' => $e->getMessage()]);
        }

        // === セッション削除 ===
        Session::forget(['cart', 'address']);

        return redirect()->route('order.complete')->with('success', '注文が完了しました。');

    } catch (\Exception $e) {
        \Log::error('AmazonPay complete() 注文処理エラー', ['error' => $e->getMessage()]);
        return redirect()->route('cart.index')->with('error', '注文処理に失敗しました: ' . $e->getMessage());
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
