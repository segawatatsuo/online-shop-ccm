<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\Action;
use Illuminate\Http\Request;
use App\Models\Order; // Orderモデルをインポート

/**
 * メール内容確認カスタムアクション
 * 注文詳細画面に表示され、発送メールのプレビューをモーダルで表示します。
 */
class CheckShippingMail extends Action
{
    // アクションボタンの表示名
    public $name = 'メール内容確認';

    // 注文IDを保持するためのプロパティ
    protected $id;

    /**
     * コンストラクタ
     *
     * @param int|null $id 注文ID
     */
    public function __construct($id = null)
    {
        parent::__construct();
        $this->id = $id; // コンストラクタでIDを受け取る
    }

    /**
     * アクションボタンのHTMLをレンダリング
     *
     * @return string
     */
    public function render()
    {
        // data-action属性とdata-id属性を使って、アクションハンドラに情報を渡します。
        return <<<HTML
        <a class="btn btn-primary btn-sm" href="javascript:void(0);" data-action="{$this->getName()}" data-id="{$this->id}">
            <i class="fa fa-envelope"></i> {$this->name}
        </a>
HTML;
    }

    /**
     * アクションが実行された際の処理
     *
     * @param Request $request HTTPリクエスト
     * @return \Encore\Admin\Actions\Response
     */
    public function handle(Request $request)
    {
        // リクエストから注文IDを取得
        $orderId = $request->get('id');

        // Orderモデルから関連する顧客情報と共にデータを取得
        $order = Order::with('customer')->find($orderId);

        // 注文が見つからない場合の処理
        if (!$order) {
            return $this->response()->error('注文が見つかりませんでした。');
        }

        // ここでメールの内容をHTML形式で構築します。
        // 実際には、Bladeテンプレートなどを使用してより複雑な内容を生成することが推奨されます。
        $mailContent = "
            <h3>発送メール内容プレビュー</h3>
            <p><strong>注文番号:</strong> " . htmlspecialchars($order->order_number) . "</p>
            <p><strong>顧客名:</strong> " . htmlspecialchars($order->customer->full_name) . "</p>
            <p><strong>メールアドレス:</strong> " . htmlspecialchars($order->customer->email) . "</p>
            <hr>
            <p>拝啓 " . htmlspecialchars($order->customer->full_name) . "様</p>
            <p>この度は、ご注文いただき誠にありがとうございます。</p>
            <p>ご注文の商品が発送されましたことをお知らせいたします。</p>
            <p>商品到着まで今しばらくお待ちください。</p>
            <p>今後ともよろしくお願い申し上げます。</p>
            <br>
            <p>〇〇ストア</p>
        ";

        // モーダルで内容を表示するためのレスポンスを返します。
        // `modal()` メソッドの第一引数はモーダルのタイトルになります。
        return $this->response()->html($mailContent)->modal('メール内容確認');
    }
}
