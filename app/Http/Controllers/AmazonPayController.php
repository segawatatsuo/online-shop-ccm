<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Services\AmazonPayService;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Delivery;
use App\Mail\OrderConfirmed;
use App\Mail\OrderNotification;

class AmazonPayController extends Controller
{
    protected $amazonPayService;

    public function __construct(AmazonPayService $amazonPayService)
    {
        $this->amazonPayService = $amazonPayService;
    }

    // 支払い確認画面
    public function showPayment(Request $request)
    {
        $amount = $request->input('amount');
        return view('amazonpay.payment', compact('amount'));
    }

    // CheckoutSession 作成（仮注文）
    public function createPaymentSession(Request $request)
    {
        $amount = $request->input('amount');
        $orderNumber = 'ORD' . now()->format('YmdHis');

        // CheckoutSession 作成
        $result = $this->amazonPayService->createPaymentSession($amount, $orderNumber);

        // 仮注文作成
        $order = Order::create([
            'order_number' => $orderNumber,
            'amount' => $amount,
            'status' => 'pending',
            'amazon_checkout_session_id' => $result['checkoutSessionId'],
        ]);

        // 仮注文に注文明細を追加
        $cart = session('cart', []);
        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item['product_id'],
                'product_code' => $item['product_code'],
                'name'         => $item['name'],
                'quantity'     => $item['quantity'],
                'price'        => $item['price'],
                'subtotal'     => $item['price'] * $item['quantity'],
            ]);
        }

        return redirect($result['webCheckoutUrl']);
    }

    // チェックアウト完了（与信確定）
    public function complete(Request $request)
    {
        $amazonCheckoutSessionId = $request->query('amazonCheckoutSessionId');
        \Log::info('AmazonPay complete() 開始', [
            'amazonCheckoutSessionId' => $amazonCheckoutSessionId
        ]);

        if (!$amazonCheckoutSessionId) {
            return redirect()->route('cart.index')
                ->with('error', 'Amazon Pay セッションIDが存在しません。');
        }

        try {
            // 仮注文から金額を取得して completePayment() に渡す
            $order = Order::where('amazon_checkout_session_id', $amazonCheckoutSessionId)->firstOrFail();
            $result = $this->amazonPayService->completePayment($amazonCheckoutSessionId);

            $order    = $result['order'];
            $customer = $result['customer'];
            $delivery = $result['delivery'];

            // メール送信
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

            // セッション削除
            Session::forget(['cart', 'address']);

            return redirect()->route('order.complete')->with('success', '注文が完了しました。');
        } catch (\Exception $e) {
            \Log::error('AmazonPay complete() 注文処理エラー', ['error' => $e->getMessage()]);
            return redirect()->route('cart.index')->with('error', '注文処理に失敗しました: ' . $e->getMessage());
        }
    }

    // キャンセル処理
    public function cancelPayment()
    {
        return redirect()->route('cart.index')->with('error', 'Amazon Pay による支払いがキャンセルされました。');
    }
}
