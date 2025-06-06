<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use App\Admin\Controllers\ProductJaController;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');

    $router->resource('product-jas', 'ProductJaController');

    // ✅ 複製用のPOSTルートをここに追加！
    Route::post('product/duplicate', [ProductJaController::class, 'duplicate']);

});
