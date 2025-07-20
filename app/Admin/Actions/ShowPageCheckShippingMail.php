<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailTemplate;
use App\Models\Order;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Admin;

class ShowPageCheckShippingMail extends Action
{
    public $name = '発送メール確認';
    protected $icon = 'fa-envelope';
    protected $id;

    public function __construct($id = null)
    {
        parent::__construct();
        $this->id = $id;
    }

    public function handle(Request $request)
    {
        // このhandleメソッドはform()が成功した後に実行されるため、
        // このデバッグでは直接関係ありませんが、完全なコードとして残します。
        $orderId = $request->input('order_id');
        $order = Order::with(['customer'])->findOrFail($orderId);

        if (empty($order->tracking_number)) {
            return $this->response()->error('配送伝票番号が入力されていません')->refresh();
        }
        if (empty($order->shipping_company)) {
            return $this->response()->error('運送会社が入力されていません');
        }
        if (empty($order->shipping_date)) {
            return $this->response()->error('発送日が未入力です');
        }

        $customer = $order->customer;
        $subject = $request->input('email_subject');
        $body = $request->input('email_body');

        try {
            Mail::send([], [], function ($message) use ($customer, $subject, $body) {
                $message->to($customer->email)
                    ->subject($subject)
                    ->setBody($body, 'text/html');
            });

            return $this->response()->success('発送確認メールを送信しました')->refresh();
        } catch (\Exception $e) {
            return $this->response()->error('送信エラー: ' . $e->getMessage())->refresh();
        }
    }

    public function form()
    {
        // --- 最小構成のform()メソッド ---
        // これでエラーが発生しないか確認してください。
        // もしこれでエラーが解消されれば、問題はメール内容生成ロジックにあります。
        $this->text('test_field_name', 'テストフィールド');

        // 必須ではありませんが、handleメソッドにIDを渡すために残します
        $this->hidden('order_id')->default($this->id);

        // ここが重要: `return $this;` を削除します。
        // 親クラスの `Action` の `form()` メソッドが自動的に `Form` インスタンスを返します。
    }
}
