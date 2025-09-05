<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductJa;
use App\Services\CartService;

class CartController extends Controller
{
    /*
    * @return \Illuminate\Http\Response HTTPリクエストを受け取り、ショッピングカートのページを表示します。
    *$cart = session()->get('cart', []);: セッションからcartキーの値を取得します。cartキーが存在しない場合は、空の配列[]をデフォルト値として使用します。
    *$user = auth()->user();: 現在認証されているユーザーの情報を取得します。ユーザーがログインしていない場合は、nullが返されます。
    * 商品ID($productId)を使用して、データベースから商品の情報を取得します。
    *$price = $user ? $product->member_price : $product->price;: ユーザーがログインしている場合は会員価格(member_price)、そうでない場合は通常価格(price)を商品の価格として設定します。
    */

    /*serviceを使う*/
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        /****
         Serviceを使うことにしたのでセッション取得などのコードを変更
        $cart = session()->get('cart', []);
        $user = auth()->user();
        $cartItems = [];
        $total = 0;
    
        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if (!$product) {
                continue; // 商品が削除された場合など
            }
    
            $price = $user ? $product->member_price : $product->price;
    
            $cartItems[] = [
                'product_id' => $productId,
                'name' => $product->name, // 商品名を追加
                'quantity' => $item['quantity'],
                'price' => $price,
            ];
        }
         $cart = $cartItems;
        return view('cart.index', compact('cart'));
        ****/
        
        $user = auth()->user();
        //Serviceのメソッド
        $result = $this->cartService->getCartItems($user);
        $cart = $result['items'];
        $total = $result['total'];
        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        /****
        $product = Product::findOrFail($request->product_id);
        $user = auth()->user();
        $price = $user ? $product->member_price : $product->price;// 会員価格かそうでないか
        $quantity = max((int) $request->input('quantity', 1), 1);// 数量を取得、最低1に制限

        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $price,
                
            ];
        }
        session()->put('cart', $cart);// ← ここでセッション名「cart」に保存される
        return redirect()->route('cart.index');
        ****/

        $product = ProductJa::findOrFail($request->product_id);
        $quantity = max((int) $request->input('quantity', 1), 1);
        $user = auth()->user();
        //Serviceのメソッド
        $this->cartService->addProduct($product, $quantity, $user);
        return redirect()->route('cart.index');
    }

    public function update(Request $request)
    {
        /****
        $cart = session()->get('cart', []);
        $productId = $request->product_id;
        $quantity = max((int)$request->quantity, 1);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'カートを更新しました。');
        ****/
        $this->cartService->updateQuantity($request->product_id, $request->quantity);
        return redirect()->route('cart.index')->with('message', 'カートを更新しました。');
    }

    public function remove(Request $request)
    {
        /****
        $cart = session()->get('cart', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', '商品をカートから削除しました。');
        ****/
        $this->cartService->removeProduct($request->product_id);
        return redirect()->route('cart.index')->with('message', '商品を削除しました');
    }

}
