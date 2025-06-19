<?php
// app/Services/CartService.php

namespace App\Services;

use App\Models\ProductJa;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getCartItems($user = null)
    {
        $cart = session()->get('cart', []);
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = ProductJa::find($productId);
            if (!$product) {
                continue;
            }

            //$price = $user ? $product->member_price : $product->price;
            $price = $item['price'] ?? ($user ? $product->member_price : $product->price);
            $subtotal = $price * $item['quantity'];

            $items[] = [
                'product_id' => $productId,
                'name' => $product->name,
                'quantity' => $item['quantity'],
                'price' => $price,
                'subtotal' => $subtotal,
            ];

            $total += $subtotal;
        }

        return [
            'items' => $items,
            'total' => $total,
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
