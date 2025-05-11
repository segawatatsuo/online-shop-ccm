<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductJa;
use App\Services\CartService;
use App\Services\AmazonPayService;

use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /*serviceを使う*/
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $user = auth()->user();
        //Serviceのメソッド
        $result = $this->cartService->getCartItems($user);
        $cart = $result['items'];
        $total = $result['total'];
        return view('cart.index', compact('cart', 'total'));
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
            /*
            Session::put('cart', [
                'items' => [
                    ['name' => '商品A', 'price' => 1000, 'qty' => 1],
                    ['name' => '商品B', 'price' => 2000, 'qty' => 2],
                ],
                'customer_name' => '山田 太郎',
                'email' => 'yamada@example.com'
            ]);
            */
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
}
