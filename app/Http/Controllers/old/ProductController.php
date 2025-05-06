<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        //ログインしていればUserモデルをしていなければnullを返す。会員価格を表示するために必要
        $user =auth()->user();
        return view('products.index', compact('products','user'));
    }

    public function show($id)
    {
        /*
        findOrFail() ではなくfind() を使用することで、モデルが見つからない場合の処理をより柔軟に制御できます。しかし、
        単に「見つからなければ404エラーを返したい」という場合は、findOrFail() を使うのが簡潔で一般的です。;
        */
        $product = Product::with('category')->findOrFail($id);
        //ログインしていればUserモデルをしていなければnullを返す。会員価格を表示するために必要
        $user =auth()->user();
        return view('products.show', compact('product','user'));
    }
}

