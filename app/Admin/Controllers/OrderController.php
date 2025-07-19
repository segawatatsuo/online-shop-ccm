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

        $grid->column('order_number', __('注文番号'));
        $grid->column('customer.sei', '姓');
        $grid->column('customer.mei', '名');
        // 金額カラムに適用するクロージャ
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


        // ここにカスタムアクションを追加
        // 各行に「発送メール送信」アクションを追加します。
        $grid->actions(function ($actions) {
            // デフォルトの表示、編集、削除ボタンを保持したい場合はコメントアウトを解除
             //$actions->disableView();
             //$actions->disableEdit();
             //$actions->disableDelete();

            $actions->add(new SendShippingMail());
        });

        // プルダウンメニューの表示問題を修正するためのCSS追加
        $grid->footer(function () {
            return '
            <style>
                .grid-actions .dropdown-menu {
                    position: fixed !important;
                    z-index: 9999 !important;
                    min-width: 120px;
                }
                .grid-actions .dropdown {
                    position: static !important;
                }
                .box-body {
                    overflow: visible !important;
                }
                .table-responsive {
                    overflow: visible !important;
                }
                .content-wrapper {
                    overflow: visible !important;
                }
                /* アクションボタンのドロップダウンメニューの位置調整 */
                .grid-actions .dropdown-menu {
                    position: absolute !important;
                    top: 100% !important;
                    left: 0 !important;
                    transform: none !important;
                }
                /* 最後の行の場合は上に表示 */
                .grid-actions .dropdown.dropup .dropdown-menu {
                    top: auto !important;
                    bottom: 100% !important;
                }
            </style>
            <script>
                $(document).ready(function() {
                    // ドロップダウンメニューの位置を動的に調整
                    $(".grid-actions .dropdown-toggle").on("click", function() {
                        var dropdown = $(this).closest(".dropdown");
                        var menu = dropdown.find(".dropdown-menu");
                        var rect = this.getBoundingClientRect();
                        var windowHeight = window.innerHeight;
                        
                        // 画面下部の場合は上に表示
                        if (rect.bottom + menu.outerHeight() > windowHeight) {
                            dropdown.addClass("dropup");
                        } else {
                            dropdown.removeClass("dropup");
                        }
                    });
                });
            </script>
            ';
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
        $order = Order::with(['customer', 'orderItems'])->findOrFail($id);
        $show = new Show($order);

        $show->field('order_number', __('注文番号'));

        // 方法1: リレーション経由でアクセサを呼び出す
        $show->field('customer.full_name', '氏名');

        $show->field('customer.zip', __('郵便番号'));
        $show->field('customer.full_address', __('住所'));
        $show->field('customer.phone', __('電話番号'));
        $show->field('customer.email', __('メールアドレス'));

        $show->field('delivery_date', __('配達希望日'));
        $show->field('delivery_time', __('配達希望時間'));
        $show->field('your_request', __('ご要望'));

        $show->field('shipping_date', __('発送日'));
        $show->field('tracking_number', __('配送伝票番号'));
        $show->field('shipping_company', __('運送会社名'));

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

        // 配送先情報
        $form->text('customer.zip', __('郵便番号'));
        $form->text('customer.input_add01', __('住所1'));
        $form->text('customer.input_add02', __('住所2'));
        $form->text('customer.input_add03', __('住所3'));
        $form->text('customer.phone', __('電話番号'));
        $form->text('customer.email', __('メールアドレス'));

        // 配達情報
        $form->date('delivery_date', __('配達希望日'));
        $form->time('delivery_time', __('配達希望時間'));
        $form->textarea('your_request', __('ご要望'));


        $form->text('shipping_date', __('発送日'));
        $form->text('tracking_number', __('配送伝票番号'));
        $form->text('shipping_company', __('運送会社名'));


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
