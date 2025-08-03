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
use App\Services\ShippingFeeService;

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
    protected $shippingFeeService;

    public function __construct(CartService $cartService, ShippingFeeService $shippingFeeService)
    {
        $this->cartService = $cartService;
        $this->shippingFeeService = $shippingFeeService;
    }

    public function create(Request $request, CartService $cartService)
    {
        // 🔽 セッションからカート(セッションデータのキー名が「cart」の情報を配列で取得。無ければ空の配列を返す）
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('warning', 'カートが空です。');
        }

        $deliveryTimes = DeliveryTime::pluck('time'); // 配送時間帯のtimeカラムの値のみを取得



        // 認証済みユーザーが法人かどうかをチェック
        /*
        $user = auth()->user();
        if ($user && $user->user_type === 'corporate') {
            // 法人ユーザーの場合ここで合計金額の表示が必要なので取得
            //法人は都道府県がすでに登録済なので送料を計算できるcorporate_customersテーブルを使う
            $prefecture = $user->corporateCustomer->delivery_add01;
            $shippingFee = $this->shippingFeeService->getFeeByPrefecture($prefecture);
            
            $total = session('total'); //CartService.phpで合計金額をセッションに保存している
            return view('order.corporate_confirm', compact('cart', 'user', 'total', 'deliveryTimes'));
        }
        // 一般ユーザー用：新規お届け先登録画面へ
        return view('order.create', compact('cart', 'deliveryTimes'));
        */
        $user = auth()->user();

        if ($user && $user->user_type === 'corporate') {
            $prefecture = $user->corporateCustomer->delivery_add01;
            // CartService は $this->cartService を使う（__construct で注入済）
            $cart = $this->cartService->getCartItems($user, $prefecture);
            return view('order.corporate_confirm', [
                'user' => $user,
                'cart' => $cart['items'],
                'subtotal' => $cart['subtotal'],
                'shipping_fee' => $cart['shipping_fee'],
                'total' => $cart['total'],
                'deliveryTimes' => $deliveryTimes,
            ]);
        }

        $prefecture = null;
        $cart = $this->cartService->getCartItems($user, $prefecture);
        return view('order.create', [
            'items' => $cart['items'],
            'subtotal' => $cart['subtotal'],
            'shipping_fee' => $cart['shipping_fee'],
            'total' => $cart['total'],
            'deliveryTimes' => $deliveryTimes, // ← これを追加！
        ]);
    }

    //バリデーションのリクエストクラスでバリデーションを行い、それをビューに送る
    public function confirm(OrderCustomerRequest $request) //バリデーションのリクエストクラス(OrderCustomerRequest)を依存注入する
    {
        // 1. 依存注入されたことによりFormRequest（OrderCustomerRequest）の rules() が自動で適用され
        // 2. バリデーションに通ると
        // 3. validated() で「検証済みの値」だけを取得
        $validatedData = $request->validated(); // 4.全てのバリデーション済みの住所データを配列で取得
/*
        $validatedData = 
        array:20 [▼
  "order_sei" => "瀬川"
  "order_mei" => "達男"
  "order_zip" => "206-0823"
  "order_email" => "segawa@lookingfor.jp"
  "order_phone" => "09091496802"
  "order_add01" => "東京都"
  "order_add02" => "稲城市平尾"
  "order_add03" => null
  "delivery_date" => null
  "delivery_time" => "なし"
  "your_request" => null
  "same_as_orderer" => "1"
  "delivery_sei" => "瀬川"
  "delivery_mei" => "達男"
  "delivery_zip" => "206-0823"
  "delivery_email" => "segawa@lookingfor.jp"
  "delivery_phone" => "09091496802"
  "delivery_add01" => "東京都"
  "delivery_add02" => "稲城市平尾"
  "delivery_add03" => null
]
*/    

        // 送料計算(コンストラクタでShippingFeeServiceを依存注入しているので、直接呼び出せる)
        //$shippingFee = $this->shippingFeeService->getFeeByPrefecture($validatedData["delivery_add01"]);
$getCartItems = $this->cartService->getCartItems(null, $validatedData["delivery_add01"]);
/* $cart=
array:4 [▼
  "items" => array:2 [▼
    0 => array:6 [▼
      "product_id" => 4
      "product_code" => "PS04"
      "name" => "エアーストッキングプレミアムシルク 120G ブロンズ"
      "quantity" => 1
      "price" => 3300
      "subtotal" => 3300
    ]
    1 => array:6 [▼
      "product_id" => 10
      "product_code" => "DL05"
      "name" => "エアーストッキングダイアモンドレッグス 120G ダンス"
      "quantity" => 1
      "price" => 4400
      "subtotal" => 4400
    ]
  ]
  "subtotal" => 7700
  "shipping_fee" => 1500
  "total" => 9200
]
*/

        // セッションに住所を保存（戻るときに使用）
        session(['address' => $validatedData]);

        return view('order.confirm',compact('getCartItems', 'validatedData'));

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
