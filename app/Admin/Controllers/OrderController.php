<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

// カスタムアクションをインポート
use App\Admin\Actions\SendShippingMail;
use App\Admin\Actions\CheckShippingMail; // 修正: CheckShippingMailAction から CheckShippingMail へ
use Carbon\Carbon;

class OrderController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '注文管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */



    protected function grid()
    {
        $grid = new Grid(new Order());

        // 作成日時の降順で表示
        $grid->model()->orderBy('created_at', 'desc');


        //ユーザーが「作成日時」のヘッダーをクリックして昇順・降順を切り替え可能に
        $grid->column('created_at', __('作成日時'))->sortable();


        // ✅ 月別絞り込み機能の追加
        if (request()->has('month')) {
            try {
                [$year, $month] = explode('-', request()->get('month'));
                $grid->model()->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);
            } catch (\Exception $e) {
                \Log::warning('不正な月パラメータ: ' . request()->get('month'));
            }
        }

        // ✅ 今日の日付だけでフィルター
        if (request()->has('date')) {
            $date = request()->get('date');
            $start = $date . ' 00:00:00';
            $end = $date . ' 23:59:59';

            $grid->model()->whereBetween('created_at', [$start, $end]);
        }


        // フィルタの追加
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();

            // 日付での範囲検索（created_at）
            $filter->between('created_at', '注文日時')->datetime();

            // カスタム日付フィルタ（例: ?date=2025-07-22 形式の対応）
            if (request()->has('date')) {
                $date = request()->get('date');
                $start = $date . ' 00:00:00';
                $end = $date . ' 23:59:59';
                $filter->where(function ($query) use ($start, $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                }, '日付指定');
            }

            // 卸売りなどのタイプがある場合（例: typeカラムがある想定）
            $filter->equal('order_type', '注文種別')->select([
                'retail' => '一般',
                'wholesale' => '卸売り',
            ]);
        });

        $grid->column('order_number', __('注文番号'));
        $grid->column('customer.sei', '姓');
        $grid->column('customer.mei', '名');

        $grid->column('total_price', __('総合計'))->display(function ($amount) {
            return '¥' . number_format($amount);
        });

        $grid->column('delivery_date', __('配送希望日'));
        $grid->column('delivery_time', __('配送希望時間'));
        $grid->column('your_request', __('メッセージ'));
        $grid->column('shipping_date', __('発送日'));
        $grid->column('tracking_number', __('配送伝票番号'));
        $grid->column('shipping_company', __('運送会社名'));
        $grid->column('created_at', __('作成日時'));

        // 行アクション（発送メール）
        $grid->actions(function ($actions) {
            $actions->add(new SendShippingMail());
        });

        // フッターにCSS（省略可）
        $grid->footer(function () {
            return <<<HTML
        <style>
            .grid-actions .dropdown-menu { position: fixed !important; z-index: 9999 !important; min-width: 120px; }
            .grid-actions .dropdown { position: static !important; }
            .box-body, .table-responsive, .content-wrapper { overflow: visible !important; }
            .grid-actions .dropdown-menu { position: absolute !important; top: 100% !important; left: 0 !important; transform: none !important; }
            .grid-actions .dropdown.dropup .dropdown-menu { top: auto !important; bottom: 100% !important; }
        </style>
        <script>
            $(document).ready(function() {
                $(".grid-actions .dropdown-toggle").on("click", function() {
                    var dropdown = $(this).closest(".dropdown");
                    var menu = dropdown.find(".dropdown-menu");
                    var rect = this.getBoundingClientRect();
                    var windowHeight = window.innerHeight;
                    if (rect.bottom + menu.outerHeight() > windowHeight) {
                        dropdown.addClass("dropup");
                    } else {
                        dropdown.removeClass("dropup");
                    }
                });
            });
        </script>
        HTML;
        });

        return $grid;
    }



    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        /*表示（編集不可画面）*/

        // EagerローディングでcustomerとorderItemsリレーションを取得
        $order = Order::with(['customer', 'orderItems', 'delivery'])->findOrFail($id);
        $show = new Show($order);

        $show->field('order_number', __('注文番号'));

        // 方法1: リレーション経由でアクセサを呼び出す
        $show->field('customer.full_name', '氏名');

        $show->field('delivery_date', __('配達希望日'));
        $show->field('delivery_time', __('配達希望時間'));
        $show->field('your_request', __('ご要望'));

        $show->field('shipping_date', __('発送日'));
        $show->field('tracking_number', __('配送伝票番号'));
        $show->field('shipping_company', __('運送会社名'));

        $show->field('customer.zip', __('注文者郵便番号'));
        $show->field('customer.full_address', __('注文者住所'));
        $show->field('customer.phone', __('注文者電話番号'));
        $show->field('customer.email', __('注文者メールアドレス'));

        $show->field('delivery.zip', __('送付先郵便番号'));
        $show->field('delivery.full_address', __('送付先住所'));
        $show->field('delivery.phone', __('送付先電話番号'));
        $show->field('delivery.email', __('送付先メールアドレス'));



        // 注文商品をテーブル形式で表示
        $show->field('orderItems', __('注文商品'))->as(function ($orderItems) use ($order) {
            $html = '<div class="table-responsive">';
            $html .= '<table class="table table-striped table-hover">';
            $html .= '<thead class="table-dark">';
            $html .= '<tr>';
            $html .= '<th scope="col">商品コード</th>';
            $html .= '<th scope="col">商品名</th>';
            $html .= '<th scope="col" class="text-center">数量</th>';
            $html .= '<th scope="col" class="text-end">単価</th>';
            $html .= '<th scope="col" class="text-end">小計</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            foreach ($orderItems as $item) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($item->product_code) . '</td>';
                $html .= '<td>' . htmlspecialchars($item->name) . '</td>';
                $html .= '<td class="text-center">' . number_format($item->quantity) . '</td>';
                $html .= '<td class="text-end">¥' . number_format($item->price) . '</td>';
                $html .= '<td class="text-end">¥' . number_format($item->subtotal) . '</td>';
                $html .= '</tr>';
            }

            // 合計行を追加（Orderモデルのtotal_priceを使用）
            $html .= '<tr class="table-info">';
            $html .= '<td colspan="4" class="text-end"><strong>合計:</strong></td>';
            $html .= '<td class="text-end"><strong>¥' . number_format($order->total_price) . '</strong></td>';
            $html .= '</tr>';

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';

            return $html;
        })->unescape();



        // 詳細画面にカスタムアクションボタンを追加
        // 方法1: HTMLとして直接追加
        $show->field('actions', __('アクション'))->as(function () use ($id) {
            $checkShippingAction = new CheckShippingMail($id);
            $url = '/admin/orders/' . $id . '/check-shipping-mail'; // 適切なURLに変更してください

            return '<a href="' . $url . '" class="btn btn-primary">
                        <i class="fa fa-envelope"></i> 発送メール確認
                    </a>';
        })->unescape();


        return $show;
    }


    protected function form()
    {
        $form = new Form(new Order());

        // 注文番号
        $form->text('order_number', __('注文番号'));

        // 顧客情報
        $form->select('customer_id', __('顧客'))->options(function ($id) {
            $customer = Customer::find($id);
            if ($customer) {
                return [$customer->id => $customer->full_name];
            }
        })->ajax('/admin/api/customers');



        // 配達情報
        $form->date('delivery_date', __('配達希望日'));
        $form->time('delivery_time', __('配達希望時間'));
        $form->textarea('your_request', __('ご要望'));


        $form->text('shipping_date', __('発送日'));
        $form->text('tracking_number', __('配送伝票番号'));
        $form->text('shipping_company', __('運送会社名'));

        // 注文者情報
        $form->text('customer.zip', __('注文者郵便番号'));
        $form->text('customer.input_add01', __('注文者住所1'));
        $form->text('customer.input_add02', __('注文者住所2'));
        $form->text('customer.input_add03', __('注文者住所3'));
        $form->text('customer.phone', __('注文者電話番号'));
        $form->text('customer.email', __('注文者メールアドレス'));

        // 配送先情報
        $form->text('delivery.zip', __('配送先郵便番号'));
        $form->text('delivery.input_add01', __('配送先住所1'));
        $form->text('delivery.input_add02', __('配送先住所2'));
        $form->text('delivery.input_add03', __('配送先住所3'));
        $form->text('delivery.phone', __('配送先電話番号'));
        $form->text('delivery.email', __('配送先メールアドレス'));


        // --- ここから商品明細部分 ---
        // HasMany関係でorderItemsを表示
        $form->hasMany('orderItems', __('注文商品'), function (Form\NestedForm $form) {
            $form->text('product_code', __('商品コード'))->rules('required');
            $form->text('name', __('商品名'))->rules('required');
            $form->decimal('price', __('単価'))->rules('required|numeric|min:0');
            $form->number('quantity', __('数量'))->rules('required|integer|min:1');
            // 小計は通常、保存時や表示時に計算されるため、編集フィールドには含めないことが多い
            // 必要であれば、displayメソッドやカスタムロジックで表示することも可能
        });
        // --- ここまで商品明細部分 ---

        // 合計金額（読み取り専用）
        // total_priceがorderItemsから自動計算される場合、hasManyの保存フックなどで更新する
        $form->currency('total_price', __('合計金額'))->symbol('¥')->readonly();

        // 保存前後のフックで合計金額を更新する例
        $form->saving(function (Form $form) {
            // orderItemsが送信された場合
            if (isset($form->orderItems)) {
                $total = 0;
                foreach ($form->orderItems as $item) {
                    // 新規追加や既存の項目でquantityとpriceがあることを確認
                    if (isset($item['quantity']) && isset($item['price'])) {
                        $total += (float) $item['quantity'] * (float) $item['price'];
                    }
                }
                $form->total_price = $total;
            }
        });


        return $form;
    }
}
