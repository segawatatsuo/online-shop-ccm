<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;


use App\Admin\Controllers\OrderItemController; // OrderItemのCRUDを管理するコントローラ
use App\Admin\Actions\SendShippingMail; // 後で作成するカスタムアクションをインポート
use App\Admin\Actions\CheckShippingMailAction; // 追加: メール内容確認アクション

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
        $grid->column('status', __('Status'));
        $grid->column('created_at', __('作成日時'));
        $grid->column('updated_at', __('修正日時'));


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

        // 詳細画面にもアクションボタンを追加
        $show->panel()
            ->tools(function ($tools) {
                $tools->add('<a class="btn btn-primary" href="javascript:void(0)" onclick="checkShippingMail(' . $this->getKey() . ')">
                    <i class="fa fa-envelope"></i> メール内容確認
                </a>');
            });

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
        $form->text('customer.full_address', __('住所'));
        $form->text('customer.phone', __('電話番号'));
        $form->text('customer.email', __('メールアドレス'));

        // 配達情報
        $form->date('delivery_date', __('配達希望日'));
        $form->time('delivery_time', __('配達希望時間'));
        $form->textarea('your_request', __('ご要望'));

        // --- ここから商品明細部分 ---
        // HasMany関係でorderItemsを表示
        $form->hasMany('orderItems', __('注文商品'), function (Form\NestedForm $form) {
            $form->text('product_code', __('商品コード'))->rules('required');
            $form->text('name', __('商品名'))->rules('required');
            $form->decimal('price', __('単価'))->rules('required|numeric|min:0');
            $form->number('quantity', __('数量'))->rules('required|integer|min:1');
            // 小計は通常、保存時や表示時に計算されるため、編集フィールドには含めないことが多い
            // 必要であれば、`display`メソッドやカスタムロジックで表示することも可能
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