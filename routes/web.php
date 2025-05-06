<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductJaController;

use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\ProductImageJaController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\AdminRegisterController;

use App\Http\Controllers\PaymentController;

// ホーム → 商品一覧にリダイレクト
//Route::get('/', fn() => redirect('/products'));

// ✅ トップページに index.blade.php を表示
Route::get('/', function () {
    return view('index'); // resources/views/index.blade.php
})->name('products.index');

// 商品
/*
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductJaController::class, 'index'])->name('index');
    Route::get('{id}', [ProductJaController::class, 'show'])->name('show');
});
*/
// 商品（カテゴリ別一覧と詳細）
Route::prefix('product')->name('product.')->group(function () {
    // カテゴリ別商品一覧
    Route::get('{category}', [ProductJaController::class, 'category'])->name('category');
    
    // 商品詳細（例: /product/airstocking/123）
    Route::get('{category}/{id}', [ProductJaController::class, 'show'])->name('show');
});


// カート
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add', [CartController::class, 'add'])->name('add');
    Route::post('update', [CartController::class, 'update'])->name('update');
    Route::post('remove', [CartController::class, 'remove'])->name('remove');
});

// 注文
Route::prefix('order')->name('order.')->group(function () {
    Route::get('confirm', [OrderController::class, 'confirm'])->name('confirm');
    Route::post('complete', [OrderController::class, 'complete'])->name('complete');
});

// 認証関連
Auth::routes();
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// 顧客マイページ（認証必須）
Route::middleware('auth')->prefix('mypage')->name('mypage.')->group(function () {
    Route::get('/', [MypageController::class, 'index'])->name('index');
    Route::get('edit', [MypageController::class, 'edit'])->name('edit');
    Route::post('update', [MypageController::class, 'update'])->name('update');

    Route::get('password', [MypageController::class, 'editPassword'])->name('password.edit');
    Route::post('password', [MypageController::class, 'updatePassword'])->name('password.update');
});

// 管理者ルート（ログイン必須）
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::resource('products', AdminProductController::class);

    // 管理者登録（ポリシー使用）
    Route::get('register', [AdminRegisterController::class, 'create'])->middleware('can:admin')->name('register');
    Route::post('register', [AdminRegisterController::class, 'store'])->middleware('can:admin');

    // 商品画像削除
    Route::delete('product-images/{id}', [ProductImageController::class, 'destroy'])->name('product_images.destroy');
});

// ホーム画面（ログイン後のリダイレクト用）
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//Square
Route::get('/pay', [PaymentController::class, 'show']);
Route::post('/process-payment', [PaymentController::class, 'process']);
