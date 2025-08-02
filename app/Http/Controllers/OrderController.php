<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Delivery;
use App\Http\Requests\OrderCustomerRequest; // 作成したリクエストクラスをuseする
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderThanksMail;
use App\Services\CartService;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

use App\Mail\OrderConfirmed;
use App\Mail\OrderNotification;
use App\Models\DeliveryTime; // 追加
use App\Models\ShippingFee;

class OrderController extends Controller
{

    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function create()
    {
        // 🔽 セッションからカート(セッションデータのキー名が「cart」の情報を配列で取得。無ければ空の配列を返す）
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('warning', 'カートが空です。');
        }

        $deliveryTimes = DeliveryTime::pluck('time'); // 配送時間帯のtimeカラムの値のみを取得

        // 認証済みユーザーが法人かどうかをチェック
        $user = auth()->user();
        if ($user && $user->user_type === 'corporate') {
            // 法人ユーザーの場合ここで合計金額の表示が必要なので取得
            $total = session('total'); //CartService.phpで合計金額をセッションに保存している
            return view('order.corporate_confirm', compact('cart', 'user', 'total', 'deliveryTimes'));
        }
        // 一般ユーザー用：新規お届け先登録画面へ
        return view('order.create', compact('cart', 'deliveryTimes'));
    }



    //確認
    //確認
    public function confirm(OrderCustomerRequest $request) //リクエストクラスを使う
    {
        $validatedData = $request->validated(); // 全てのバリデーション済みデータを配列で取得
        // セッションに保存（戻るときに使用）
        session(['address' => $validatedData]);
        return view('order.confirm', ['address' => $validatedData]);
    }

    public function hoge(Request $request)
    {
        $address = Session::get('address');
        $cart = Session::get('cart');

        if (!$address || !$cart) {
            return redirect()->back()->with('error', 'カートまたは住所情報が見つかりません。');
        }

        DB::beginTransaction();

        try {
            // 1. 顧客情報の保存
            $customer = Customer::create([
                'sei'     => $address['order_sei'],
                'mei'     => $address['order_mei'],
                'email'    => $address['order_email'],
                'phone'    => $address['order_phone'],
                'zip'      => $address['order_zip'],
                'input_add01' => $address['order_add01'],
                'input_add02' => $address['order_add02'],
                'input_add03' => $address['order_add03'],
            ]);

            // 2. 配送先の保存
            if ($address['same_as_orderer'] == '1') {
                $delivery = Delivery::create([
                    'sei'     => $customer->sei,
                    'mei'     => $customer->mei,
                    'email'    => $customer->email,
                    'phone'    => $customer->phone,
                    'zip'      => $customer->zip,
                    'input_add01' => $customer->input_add01,
                    'input_add02' => $customer->input_add02,
                    'input_add03' => $customer->input_add03,
                ]);
            } else {
                $delivery = Delivery::create([
                    'sei'     => $address['delivery_sei'],
                    'mei'     => $address['delivery_mei'],
                    'email'    => $address['delivery_email'],
                    'phone'    => $address['delivery_phone'],
                    'zip'      => $address['delivery_zip'],
                    'input_add01' => $address['delivery_add01'],
                    'input_add02' => $address['delivery_add02'],
                    'input_add03' => $address['delivery_add03'],
                ]);
            }

            // 3. 注文番号生成
            $orderNumber = Order::generateOrderNumber();

            // 4. 注文作成
            $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id'  => $customer->id,
                'delivery_id'  => $delivery->id,
                'total_price'  => $total,
                'delivery_time' => $address['delivery_time'],
                'delivery_date' => $address['delivery_date'],
                'your_request' => $address['your_request']
            ]);

            // 5. 商品ごとの注文保存
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'product_code' => $item['product_code'],
                    'name'       => $item['name'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'subtotal'   => $item['price'] * $item['quantity'],
                ]);
            }

            // DBコミット - ここまでで注文データの保存完了
            DB::commit();

            \Log::info('注文データ保存完了', [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('OrderController::hoge - 注文データ保存エラー', [
                'error' => $e->getMessage(),
                'exception' => $e,
                'address_session' => Session::get('address'),
                'cart_session' => Session::get('cart')
            ]);
            return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }

        // 注文保存成功後、メール送信を試行（失敗してもユーザーは完了画面へ）
        try {
            Mail::to($customer->email)->send(new OrderConfirmed($order, $customer, $delivery));
            \Log::info('顧客向け注文確認メール送信完了', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            \Log::error('顧客向け注文確認メール送信失敗', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'customer_email' => $customer->email
            ]);
            // メール送信失敗でもユーザーには成功画面を表示
            // 管理者に別途通知するなどの対応を検討
        }

        try {
            $shopEmail = 'segawa82@nifty.com';
            Mail::to($shopEmail)->send(new OrderNotification($order, $customer, $delivery));
            \Log::info('ショップ向け注文通知メール送信完了', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            \Log::error('ショップ向け注文通知メール送信失敗', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'shop_email' => $shopEmail
            ]);
        }

        // セッションクリア
        Session::forget(['cart', 'address']);

        // 注文完了画面にリダイレクト
        return redirect()->route('order.complete')->with('success', '注文が完了しました。');
    }
    // 注文番号の生成（例: ORD202505300001） モデルに移行
    /*
    private function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $latestOrder = Order::whereDate('created_at', now()->toDateString())->latest('id')->first();
        $number = $latestOrder ? ((int)substr($latestOrder->order_number, -4)) + 1 : 1;
        return 'ORD' . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
    */





    public function complete()
    {
        return view('order.complete'); // ビューは resources/views/order/complete.blade.php など
    }

    public function modify($type)
    {
        $user = auth()->user();
        return view('order.modify_address', compact('type', 'user')); // ビューは resources/views/order/complete.blade.php など
    }
}
