<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use App\Admin\Controllers\ProductJaController;
use App\Admin\Controllers\EmailTemplateController;
use App\Admin\Controllers\ShippingMailController;
use App\Admin\Controllers\AdminNoticeController;
use Encore\Admin\Facades\Admin;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');

    //商品メニュー
    $router->resource('product-jas', 'ProductJaController');

    //売上メニュー
    $router->resource('orders', 'OrderController');


    //法人顧客メニュー
    $router->resource('corporate_customers','CorporateCustomerController');

    //顧客メニュー
    $router->resource('customers','CustomerController');

    //トップページメニュー
    $router->resource('top_pages','TopPageController');

    //メールテンプレート
    $router->resource('email-templates', 'EmailTemplateController');

    //セッティング
    $router->resource('settings', 'SettingController');

    //会社情報
    $router->resource('company_infos', 'CompanyInfoController');

    //社内のお知らせ
     $router->resource('admin-notices', 'AdminNoticeController');

    // ✅ 複製用のPOSTルートをここに追加！
    Route::post('product/duplicate', [ProductJaController::class, 'duplicate']);

});
