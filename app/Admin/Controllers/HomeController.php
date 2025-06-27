<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;

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
        //本日の注文件数
        $todayOrderCount = Order::whereDate('created_at', Carbon::today())->count();

        //本月の注文金額
        $now = Carbon::now();
        $monthlyOrderSum = Order::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('total_price');

        return $content
            ->title('ダッシュボード')
            ->description('CCM 国内 管理画面')
            ->row(function ($row) use ($todayOrderCount, $monthlyOrderSum) {
                // 情報ボックス（上部に3つ並べる）
                $row->column(4, new InfoBox('本日の注文数', 'shopping-cart', 'green', '/admin/orders', $todayOrderCount));
                $row->column(4, new InfoBox('今月の売上', 'yen', 'blue', '/admin/orders', '¥' . number_format($monthlyOrderSum) ));
                $row->column(4, new InfoBox('卸売り', 'archive', 'red', '/admin/products/low-stock', '¥60,000'));
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
