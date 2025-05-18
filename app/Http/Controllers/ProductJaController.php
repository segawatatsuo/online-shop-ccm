<?php

namespace App\Http\Controllers;

use App\Models\ProductJa;

class ProductJaController extends Controller
{
    public function index()
    {
        $products = ProductJa::with('category')->get();
        if ($products->isEmpty()) {
            abort(404); // ← ここで落ちる可能性あり
        }
        $user = auth()->user();
        return view('products.index', compact('products', 'user'));
    }

    /*
    public function show($id)
    {
        $product = ProductJa::with('category')->findOrFail($id);
        //ログインしていればUserモデルをしていなければnullを返す。会員価格を表示するために必要
        $user = auth()->user();
        return view('products.show', compact('product', 'user'));
    }
        
    public function category($category)
    {
        // カテゴリ名が「airstocking」「gelnail」などのスラッグで来ると仮定
        $products = ProductJa::whereHas('category', function ($query) use ($category) {
            $query->where('slug', $category); // slugはURL用のカテゴリ識別子
        })->get();

        return view('products.category', compact('products', 'category'));
    }


    public function category($category)
    {
        // カテゴリに属する商品を取得
        $products = ProductJa::with('category')
            ->whereHas('category', function ($query) use ($category) {
                $query->where('slug', $category);
            })
            ->get();
    
        // classificationごとにグループ化
        $grouped = $products->groupBy('classification');
    
        return view('products.category', [
            'groupedProducts' => $grouped,
            'category' => $category,
        ]);
    }


    public function category($category)
    {
        // 共通の絞り込み条件
        $baseQuery = ProductJa::with('category')
            ->whereHas('category', function ($query) use ($category) {
                $query->where('slug', $category);
            });

        // 特定のclassificationごとに取得
        $premiumSilk = (clone $baseQuery)
            ->where('classification', 'Premium Silk')
            ->get();

        $diamondLegs = (clone $baseQuery)
            ->where('classification', 'Diamond Legs')
            ->get();

        return view('products.category', [
            'category' => $category,
            'premiumSilk' => $premiumSilk,
            'diamondLegs' => $diamondLegs,
        ]);
    }
*/

    public function category($category)
    {
        //collect() は空のコレクションを作るLaravelの関数です。これで変数が常に存在し、Bladeでエラーになりません。
        $premiumSilk = collect();
        $diamondLegs = collect();

        if ($category === 'airstocking') {
            $baseQuery = ProductJa::with('category')
                ->whereHas('category', function ($query) use ($category) {
                    $query->where('brand', $category);
                });

            $premiumSilk = (clone $baseQuery)
                ->where('classification', 'Premium Silk')
                ->get();

            $diamondLegs = (clone $baseQuery)
                ->where('classification', 'Diamond Legs')
                ->get();
        }

        return view('products.category', [
            'category' => $category,
            'premiumSilk' => $premiumSilk,
            'diamondLegs' => $diamondLegs,
        ]);
    }




    public function show($category, $id)
    {
        $product = ProductJa::with('category')
            ->where('id', $id)
            ->whereHas('category', function ($query) use ($category) {
                $query->where('brand', $category);
            })
            ->firstOrFail();

        $user = auth()->user();
        return view('products.show', compact('product', 'user'));
    }
}
