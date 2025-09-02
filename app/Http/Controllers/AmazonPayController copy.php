<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AmazonPayService;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmed;
use App\Mail\OrderNotification;

class AmazonPayController extends Controller
{
    protected $amazonPayService;

    public function __construct(AmazonPayService $amazonPayService)
    {
        $this->amazonPayService = $amazonPayService;
    }

    /**
     * 支払いページ表示
     */
    public function showPayment()
    {
        return view('amazonpay.payment');
    }

    /**
     * 決済セッション作成
     */
    public function createSession(Request $request)
    {
        $amount = $request->input('amount', 1000);

        try {
            // サービスに委譲して CheckoutSession 作成
            $paymentData = $this->amazonPayService->createPaymentSession($amount);

                    // $amount を追加で渡す
        $paymentData['amount'] = $amount;

            return view('amazonpay.payment_confirm', $paymentData);

        } catch (\Exception $e) {
            \Log::error('AmazonPay createSession エラー: '.$e->getMessage());
            return redirect()->back()->with('error', '決済準備中にエラーが発生しました')->withInput();
        }
    }

    /**
     * 決済完了処理
     */
public function complete(Request $request)
{
    $amazonCheckoutSessionId = $request->query('amazonCheckoutSessionId');
    $amount = session('payment_amount');

    // デバッグログ追加
    \Log::info('Amazon Pay complete called:', [
        'sessionId' => $amazonCheckoutSessionId,
        'amount' => $amount,
        'query' => $request->query(),
    ]);

    // セッションと Amazon CheckoutSessionId の確認
    if (empty($amazonCheckoutSessionId) || empty($amount)) {
        \Log::warning('Amazon Pay complete: Missing required data', [
            'sessionId' => $amazonCheckoutSessionId,
            'amount' => $amount,
        ]);
        
        return redirect()->route('amazon-pay.error')
            ->with('error', '決済情報が見つかりません。もう一度お試しください。');
    }

    try {
        // サービスで決済完了処理
        $paymentResult = $this->amazonPayService->completePayment($amazonCheckoutSessionId, $amount);

        // 注文を DB に保存
        $order = Order::create([
            'user_id' => auth()->id(),
            'email' => $paymentResult['email'],
            'amount' => $amount,
            'status' => 'paid',
            'amazon_checkout_session_id' => $amazonCheckoutSessionId,
        ]);

        // メール送信
        Mail::to($order->email)->send(new OrderConfirmed($order));
        Mail::to(config('shop.admin_email'))->send(new OrderNotification($order));

        // セッション消去
        session()->forget(['payment_amount', 'cart']);

        return view('order.complete', compact('order'));

    } catch (\Exception $e) {
        \Log::error('AmazonPay complete エラー:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'sessionId' => $amazonCheckoutSessionId,
            'amount' => $amount,
        ]);
        
        return redirect()->route('amazon-pay.error')
            ->with('error', '決済処理中にエラーが発生しました。エラーID: ' . substr(md5($e->getMessage() . time()), 0, 8));
    }
}

    /**
     * キャンセル
     */
    public function cancelPayment()
    {
        return view('amazonpay.cancel');
    }

    /**
     * エラー表示
     */
    public function errorPayment()
    {
        return view('amazonpay.error');
    }
}
