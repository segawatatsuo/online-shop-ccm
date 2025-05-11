<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Http\Requests\OrderCustomerRequest;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use App\Mail\OrderThanksMail;
use App\Services\CartService;

class OrderController extends Controller
{

    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function confirm()
    {
        // 🔽 セッションからカート(セッションデータのキー名が「cart」の情報を配列で取得。無ければ空の配列を返す）
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('warning', 'カートが空です。');
        }

        return view('order.confirm', compact('cart'));
    }


    public function complete(OrderCustomerRequest $request)
    {

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('warning', 'カートが空です。');
        }

        // 🔽 バリデーション済データの取得
        $validated = $request->validated();

        // 顧客を作成
        $customer = Customer::create($validated);

        // 注文処理
        $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        $order = Order::create([
            'customer_id' => $customer->id,
            'total_price' => $totalPrice,
            'status' => 'pending',
            //'user_id' => Auth::user()->id,
            'user_id' => optional(Auth::user())->id,
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'subtotal'   => $item['price'] * $item['quantity'],
            ]);
        }

        session()->forget('cart');

        // ✉️ メール送信
        //Mail::to($customer->email)->send(new OrderThanksMail($order));
        Mail::to($order->customer->email)->send(new OrderThanksMail($order));
        //注文確定時にカートを空にするServiceを使う
        $this->cartService->clear();

        return view('order.complete', compact('order'));
    }
}
