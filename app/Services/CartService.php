<?php
// app/Services/CartService.php

namespace App\Services;

use App\Models\ProductJa;
use Illuminate\Support\Facades\Session;
use App\Services\ShippingFeeService;

class CartService
{
    protected $shippingFeeService;

    public function __construct(ShippingFeeService $shippingFeeService)
    {
        $this->shippingFeeService = $shippingFeeService;
    }


    public function getCartItems($user = null, string $prefecture = null)
    {
        $cart = session()->get('cart', []);
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = ProductJa::find($productId);
            if (!$product) {
                continue;
            }
            $price = $item['price'];
            $subtotal = $price * $item['quantity'];


            $items[] = [
                'product_id' => $productId,
                'product_code' => $product->product_code,
                'name' => $product->name,
                'quantity' => $item['quantity'],
                'price' => $price,
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
        }

        // 都道府県に基づく送料取得
        $shippingFee = $prefecture
            ? $this->shippingFeeService->getFeeByPrefecture($prefecture)
            : 0;

        $grandTotal = $total + $shippingFee;

        session([
            'total' => $grandTotal,
            'shipping_fee' => $shippingFee
        ]);

        return [
            'items' => $items,
            'subtotal' => $total,
            'shipping_fee' => $shippingFee,
            'total' => $grandTotal,
        ];
    }

    public function addProduct($product, $quantity, $user = null)
    {
        //$price = $user ? $product->member_price : $product->price;
        $price = $product->price;
        $cart = session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'product_code' => $product->product_code,
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $price,
            ];
        }
        session()->put('cart', $cart);
        //dd(session('cart'));
    }

    public function updateQuantity($productId, $quantity)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = max(1, (int) $quantity);
            session()->put('cart', $cart);
        }
    }

    public function removeProduct($productId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }
    }

    public function clear()
    {
        session()->forget('cart');
    }
}
