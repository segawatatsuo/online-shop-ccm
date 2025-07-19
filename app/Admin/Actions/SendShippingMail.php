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

        // メールテンプレート取得（slug = thank-you-mail）
        $template = EmailTemplate::where('slug', 'thank-you-mail')->first();

        if (!$template) {
            return $this->response()->error('メールテンプレートが見つかりません')->refresh();
        }

        // メール本文
        $subject = $template->subject ?? '発送完了のお知らせ';
        $body = $template->body;

        // 差し込み（置き換え）
        $body = str_replace(
            ['{name}', '{order_number}', '{shipping_date}', '{shipping_company}', '{tracking_number}'],
            [$customer->full_name, $order->order_number, $order->shipping_date, $order->shipping_company, $order->tracking_number],
            $body
        );

        // 実際にメール送信
        try {
            Mail::raw($body, function ($message) use ($customer, $subject) {
                $message->to($customer->email)
                    ->subject($subject);
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

