<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order; // Orderモデルをインポート
use App\Models\EmailTemplate; // EmailTemplateモデルをインポート
use App\Models\Setting; // Settingモデルをインポート
use App\Models\CompanyInfo; // CompanyInfoモデルをインポート

class ShippingNotificationMail extends Mailable implements ShouldQueue // キューを使う場合はShouldQueueを実装
{
    use Queueable, SerializesModels;

    public $order;
    public $subject;
    public $body;
    public $footer;
    public $companyCcEmail;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;

        // メールテンプレートから件名と本文を取得
        $template = EmailTemplate::where('slug', 'thank-you-mail')->first();
        if ($template) {
            $this->subject = $template->subject;
            $this->body = $template->body;
        } else {
            $this->subject = 'ご注文商品の発送が完了いたしました'; // デフォルト
            $this->body = '本文のテンプレートが見つかりませんでした。'; // デフォルト
        }

        // 共通フッターを取得
        $footerSetting = Setting::where('key', 'mail_footer')->first();
        $this->footer = $footerSetting ? $footerSetting->value : '';

        // CCアドレスを取得
        $companyCc = CompanyInfo::where('key', 'cc_mail_address')->first();
        $this->companyCcEmail = $companyCc ? $companyCc->value : null;

        // 本文への差し込み処理
        $this->replacePlaceholders();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject($this->subject)
                     ->view('emails.shipping_notification'); // Bladeテンプレートを指定

        if ($this->companyCcEmail) {
            $mail->cc($this->companyCcEmail);
        }

        return $mail;
    }

    /**
     * 本文のプレースホルダーを実際のデータに置換する
     * このメソッドはコンストラクタ内で呼び出す
     */
    protected function replacePlaceholders()
    {
        // 顧客情報
        $this->body = str_replace('{{customer_name}}', $this->order->customer->name, $this->body);
        $this->body = str_replace('{{customer_address}}', $this->order->customer->address, $this->body);
        $this->body = str_replace('{{delivery_name}}', $this->order->delivery_name, $this->body);
        $this->body = str_replace('{{delivery_address}}', $this->order->delivery_address, $this->body);

        // 注文情報
        $this->body = str_replace('{{shipping_date}}', $this->order->shipping_date ? $this->order->shipping_date->format('Y年m月d日') : '未定', $this->body);
        $this->body = str_replace('{{shipping_company}}', $this->order->shipping_company, $this->body);
        $this->body = str_replace('{{tracking_number}}', $this->order->tracking_number, $this->body);
        $this->body = str_replace('{{order_id}}', $this->order->id, $this->body); // 注文IDも差し込みたい場合

        // 注文商品の情報（複数行対応）
        $itemsHtml = '';
        foreach ($this->order->orderItems as $item) {
            $itemsHtml .= "<tr>";
            $itemsHtml .= "<td>{$item->product_code}</td>";
            $itemsHtml .= "<td>{$item->product_name}</td>";
            $itemsHtml .= "<td>" . number_format($item->price) . "円</td>";
            $itemsHtml .= "<td>{$item->quantity}</td>";
            $itemsHtml .= "<td>" . number_format($item->subtotal) . "円</td>";
            $itemsHtml .= "</tr>";
        }

        // 商品情報のプレースホルダーを置換。ここではHTMLテーブル形式を想定
        $this->body = str_replace('{{order_items}}', $itemsHtml, $this->body);

        // フッターの差し込み
        $this->body = str_replace('{{footer}}', $this->footer, $this->body);
    }
}