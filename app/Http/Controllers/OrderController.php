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

class OrderController extends Controller
{

    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function create() // 前confirm
    {
        // 🔽 セッションからカート(セッションデータのキー名が「cart」の情報を配列で取得。無ければ空の配列を返す）
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('warning', 'カートが空です。');
        }
        
        $deliveryTimes = DeliveryTime::pluck('time'); // 配送時間帯のtimeカラムの値のみを取得

        return view('order.create', compact('cart', 'deliveryTimes'));
    }

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
                // 注文者と同じ場合、配送先をコピー
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
                //dd($delivery);
            } else {
                // 配送先が異なる場合
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
            $orderNumber = $this->generateOrderNumber();


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
                    'name'       => $item['name'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            DB::commit();

                        // ...（注文保存後）
            Mail::to($customer->email)->send(new OrderConfirmed($order, $customer, $delivery));

            Mail::to('shop@example.com')->send(new OrderNotification($order, $customer, $delivery));
            

            Session::forget(['cart', 'address']);

            //GET
            return redirect()->route('order.complete')->with('success', '注文が完了しました。');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('OrderController::hoge - Error during order save: ' . $e->getMessage(), ['exception' => $e, 'address_session' => Session::get('address'), 'cart_session' => Session::get('cart')]);
            return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }
    // 注文番号の生成（例: ORD202505300001）
    private function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $latestOrder = Order::whereDate('created_at', now()->toDateString())->latest('id')->first();
        $number = $latestOrder ? ((int)substr($latestOrder->order_number, -4)) + 1 : 1;
        return 'ORD' . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }


    public function complete()
    {
        return view('order.complete'); // ビューは resources/views/order/complete.blade.php など
    }

}
