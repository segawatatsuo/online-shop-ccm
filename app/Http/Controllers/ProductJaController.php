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
                ->where('not_display', '=', 0)
                ->with('mainImage')
                ->get();

            $diamondLegs = (clone $baseQuery)
                ->where('classification', 'Diamond Legs')
                ->where('not_display', '=', 0)
                ->with('mainImage')
                ->get();
        }

        //dd($premiumSilk, $diamondLegs);

        return view('products.category', [
            'category' => $category,
            'premiumSilk' => $premiumSilk,
            'diamondLegs' => $diamondLegs
          ]);
    }




    public function show($category, $id)
    {
        /*
        $product = ProductJa::with('category')
            ->where('id', $id)
            ->whereHas('category', function ($query) use ($category) {
                $query->where('brand', $category);
            })
            ->firstOrFail();*/

        $product = ProductJa::with(['category', 'mainImage', 'subImages'])
        ->where('id', $id)
        ->whereHas('category', function ($query) use ($category) {
            $query->where('brand', $category);
        })
        ->firstOrFail();

        $user = auth()->user();

        return view('products.show', compact('product', 'user'));
    }
}
