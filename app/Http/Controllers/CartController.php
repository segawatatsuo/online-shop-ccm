<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductJa;
use App\Services\CartService;
use App\Services\AmazonPayService;
use Illuminate\Support\Facades\Session;
use App\Services\ShippingFeeService;

class CartController extends Controller
{
    /*serviceを使う*/
    protected $cartService;
    protected $shippingFeeService;

    public function __construct(CartService $cartService, ShippingFeeService $shippingFeeService)
    {
        $this->cartService = $cartService;
        $this->shippingFeeService = $shippingFeeService;
    }



    public function index()
    {
        $user = auth()->user();
        $result = $this->cartService->getCartItems($user);

        $cart       = $result['items'];
        $subtotal   = $result['subtotal'];
        $shipping   = $result['shipping_fee'];
        $total      = $result['total'];

        $category = session()->get('category');
        return view('cart.index', compact('cart', 'subtotal', 'shipping', 'total', 'category'));
    }

    public function add(Request $request)
    {

        $product = ProductJa::findOrFail($request->product_id);
        $quantity = max((int) $request->input('quantity', 1), 1);
        $user = auth()->user();
        //Serviceのメソッド
        $this->cartService->addProduct($product, $quantity, $user);
        return redirect()->route('cart.index');
    }

    public function update(Request $request)
    {
        //Serviceのメソッド
        $this->cartService->updateQuantity($request->product_id, $request->quantity);
        return redirect()->route('cart.index')->with('message', 'カートを更新しました。');
    }

    public function remove(Request $request)
    {
        //Serviceのメソッド
        $this->cartService->removeProduct($request->product_id);
        return redirect()->route('cart.index')->with('message', '商品を削除しました');
    }

    //AmazonPay用
    public function show()
    {
        try {
            $cart = session('cart');
            return view('cart.show', compact('cart'));
        } catch (\Throwable $e) {
            return response("エラー: " . $e->getMessage(), 500);
        }
    }
    public function createCheckoutSession(Request $request, AmazonPayService $amazonPay)
    {
        $cart = session('cart');

        if (!$cart || !isset($cart['items'])) {
            return response()->json(['error' => 'カート情報が取得できません'], 400);
        }


        $session = $amazonPay->createCheckoutSessionForCart($cart);

        return response()->json($session);
    }

    public function review()
    {
        return '注文完了ページ（ここで注文をDBに保存など）';
    }

    public function squarePayment(Request $request)
    {
        // Orderコントローラーのconfirmアクションによってセッションに保存された値を取得(一般会員用)
        $prefecture = session('address')['delivery_add01'] ?? null; //
        $cart = $this->cartService->getCartItems(null, $prefecture);
        $totalAmount = $cart['total'];

        //法人顧客
        $user = auth()->user();
        if ($user && $user->user_type === 'corporate') {
            $prefecture = $user->corporateCustomer->delivery_add01;
            $cart = $this->cartService->getCartItems($user, $prefecture);
            $totalAmount = $cart['total'];
        }


        return view('cart.square-payment', [
            'totalAmount' => $totalAmount,
        ]);
    }

    public function whichPayment()
    {
        return view('cart.which-payment-service');
    }
}
