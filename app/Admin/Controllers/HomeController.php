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
use App\Models\AdminNotice;

class HomeController extends Controller
{
public function index(Content $content)
{
    // 各種URL（後で変更しやすくなる）
    //$todayUrl = admin_url('/orders?created_at=' . Carbon::today()->toDateString());
    $todayUrl = admin_url('/orders?date=' . Carbon::today()->toDateString());
    $monthlyUrl = admin_url('/orders?month=' . Carbon::now()->format('Y-m')); // カスタムフィルタ対応前提
    $wholesaleUrl = admin_url('/orders?type=wholesale'); // 卸売りと識別できる条件に合わせて調整

    // 本日の注文件数
    $todayOrderCount = Order::whereDate('created_at', Carbon::today())->count();

    // 本月の注文金額
    $now = Carbon::now();
    $monthlyOrderSum = Order::whereYear('created_at', $now->year)
        ->whereMonth('created_at', $now->month)
        ->sum('total_price');

    return $content
        ->title('ダッシュボード')
        ->description('CCM 国内 管理画面')
        ->row(function ($row) use ($todayOrderCount, $monthlyOrderSum, $todayUrl, $monthlyUrl, $wholesaleUrl) {

            $row->column(4, new InfoBox('本日の注文数', 'shopping-cart', 'green', $todayUrl, $todayOrderCount));
            $row->column(4, new InfoBox('今月の売上', 'yen', 'blue', $monthlyUrl, '¥' . number_format($monthlyOrderSum)));
            $row->column(4, new InfoBox('卸売り', 'archive', 'red', $wholesaleUrl, '¥60,000'));
        })
        ->row(function ($row) {
            $recentOrders = view('dashboard.recent-orders');
            $row->column(6, new Box('最近の注文', $recentOrders));

            $popularProducts = view('dashboard.popular-products');
            $row->column(6, new Box('人気商品', $popularProducts));
        })
        ->row(function ($row) {
            $notices = \App\Models\AdminNotice::orderBy('created_at', 'desc')->take(3)->get();

            $html = '';
            foreach ($notices as $notice) {
                $html .= "<h4>{$notice->title}</h4><p>{$notice->content}</p><hr>";
            }

            $row->column(12, new Box('お知らせ', $html));
        });
}

}
