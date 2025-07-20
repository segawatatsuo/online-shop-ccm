<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailTemplate;
use App\Models\Order;
use Carbon\Carbon;

class SendShippingMail extends RowAction
{
    public $name = '発送メール送信';

    public function handle(Model $order, Request $request)
    {
        // --- バリデーション ---
        $today = Carbon::today();
        if (empty($order->tracking_number)) {
            return $this->response()->error('配送伝票番号が入力されていません')->refresh();
        }

        if (empty($order->shipping_company)) {
            return $this->response()->error('運送会社が入力されていません')->refresh();
        }

        if (empty($order->shipping_date)) {
            return $this->response()->error('発送日が未入力です')->refresh();
        }
        /*
        if (Carbon::parse($order->shipping_date)->lt($today)) {
            return $this->response()->error('発送日（shipping_date）が本日より前です')->refresh();
        }*/

        // 関連顧客情報を取得
        $customer = $order->customer;
        $delivery = $order->delivery;

        // メールテンプレート取得（slug = thank-you-mail）
        $template = EmailTemplate::where('slug', 'thank-you-mail')->first();

        if (!$template) {
            return $this->response()->error('メールテンプレートが見つかりません')->refresh();
        }


//明細部分を取り出す
$orderItemsHtml = '<table border="1" cellpadding="5" cellspacing="0">';
$orderItemsHtml .= '<tr><th>商品ID</th><th>商品名</th><th>数量</th><th>単価</th><th>小計</th></tr>';

foreach ($order->orderItems as $item) {
    $orderItemsHtml .= '<tr>';
    $orderItemsHtml .= '<td>' . $item->product_id . '</td>';
    $orderItemsHtml .= '<td>' . $item->name . '</td>';
    $orderItemsHtml .= '<td>' . $item->quantity . '</td>';
    $orderItemsHtml .= '<td>' . number_format($item->price) . '円</td>';
    $orderItemsHtml .= '<td>' . number_format($item->subtotal) . '円</td>';
    $orderItemsHtml .= '</tr>';
}

$orderItemsHtml .= '</table>';


        // メール本文
        $subject = $template->subject ?? '発送完了のお知らせ';
        $body = $template->body;

        // 差し込み（置き換え）
        $body = str_replace(
            [
                '{{name}}',
                '{{order_number}}',
                '{{shipping_date}}',
                '{{shipping_company}}',
                '{{tracking_number}}',
                '{{customer_name}}',
                '{{customer_zip}}',
                '{{customer_address}}',
                '{{customer_phone}}',
                '{{delivery_name}}',
                '{{delivery_zip}}',
                '{{delivery_address}}',
                '{{delivery_phone}}',
                '{{order_items}}',
                '{{shipping}}',
                '{{total_amount}}',
                '{{footer}}'
            ],
            [
                $customer->full_name,
                $order->order_number,
                $order->shipping_date,
                $order->shipping_company,
                $order->tracking_number,
                $customer->full_name,
                $customer->zip,
                $customer->full_address,
                $customer->phone,
                $delivery->full_name,
                $delivery->zip,
                $delivery->full_address,
                $delivery->phone,

        $orderItemsHtml, // ← 差し込み内容
        number_format($order->shipping_fee) . '円',
        number_format($order->total_amount) . '円',
        'ご利用ありがとうございました。'


            ],
            $body
        );

        /*
        try {
            Mail::raw($body, function ($message) use ($customer, $subject) {
                $message->to($customer->email)
                    ->subject($subject);
            });

            return $this->response()->success('発送メールを送信しました')->refresh();
        } catch (\Exception $e) {
            return $this->response()->error('送信エラー: ' . $e->getMessage())->refresh();
        }
            */

//今は Mail::raw() を使っていますが、HTMLの <table> を使う場合は Mail::send() を使って、HTML対応にする必要があります：
try {
    Mail::send([], [], function ($message) use ($customer, $subject, $body) {
        $message->to($customer->email)
            ->subject($subject)
            ->setBody($body, 'text/html'); // ← HTMLメールとして送信
    });

    return $this->response()->success('発送メールを送信しました')->refresh();
} catch (\Exception $e) {
    return $this->response()->error('送信エラー: ' . $e->getMessage())->refresh();
}


    }

    public function dialog()
    {
        $this->confirm('この注文の発送メールを送信しますか？');
    }
}
