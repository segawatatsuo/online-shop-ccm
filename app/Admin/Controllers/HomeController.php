<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Box;


class HomeController extends Controller
{
    public function index(Content $content)
    {
        /*
        return $content
            ->title('CCM Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())

            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
            */
    return $content
        ->title('ダッシュボード')
        ->description('CCM 国内 管理画面')
        ->row(function ($row) {

            // 情報ボックス（上部に3つ並べる）
            $row->column(4, new InfoBox('本日の注文数', 'shopping-cart', 'green', '/admin/orders', 3));
            $row->column(4, new InfoBox('今月の売上', 'yen', 'blue', '/admin/orders', '¥123,000'));
            $row->column(4, new InfoBox('在庫が少ない商品', 'warning', 'red', '/admin/products/low-stock', 5));
        })
        ->row(function ($row) {

            // 最近の注文一覧
            $recentOrders = view('dashboard.recent-orders'); // Bladeで管理
            $row->column(6, new Box('最近の注文', $recentOrders));

            // 人気商品
            $popularProducts = view('dashboard.popular-products');
            $row->column(6, new Box('人気商品', $popularProducts));
        })
        ->row(function ($row) {

            // お知らせ
            $notice = '<p>ショップ運営に関するお知らせをここに記載できます。</p>';
            $row->column(12, new Box('お知らせ', $notice));
        });
    }
}
