<?php

namespace App\Admin\Actions;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailTemplate;
use App\Models\Order;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Admin; // Adminファサードをインポート

class SendShippingMail extends RowAction
{
    public $name = '発送メール送信';

    /**
     * プレビュー表示のためのフォームを定義します。
     * このメソッドは、アクションがクリックされた際にモーダルフォームを表示するためにEncore Adminによって呼び出されます。
     *
     * @return Form
     */
    public function form()
    {
        // RowActionから現在のモデルインスタンス（注文情報）を取得します。
        //$order = $this->row;

        // ここが重要：現在の行のIDを確実に取得し、Orderモデルをロード
        $orderId = $this->getKey();
        $order = Order::with(['customer', 'delivery', 'orderItems'])->find($orderId); // 関連リレーションも一緒にロード

        if (!$order) {
            return $this->response()->error('注文情報が見つかりません。リフレッシュして再度お試しください。');
        }




        $today = Carbon::today();

        // プレビュー前に基本的なバリデーションを行います。
        // これらのチェックはメールを生成するために必要です。
        if (empty($order->tracking_number)) {
            // エラーメッセージを返してフォームの表示を中断します。
            return $this->response()->error('配送伝票番号が入力されていません');
        }
        if (empty($order->shipping_company)) {
            return $this->response()->error('運送会社が入力されていません');
        }
        if (empty($order->shipping_date)) {
            return $this->response()->error('発送日が未入力です');
        }

        // 関連する顧客情報と配送先情報を取得します。
        $customer = $order->customer;
        $delivery = $order->delivery;

        // メールテンプレート（slugが'thank-you-mail'のもの）を取得します。
        $template = EmailTemplate::where('slug', 'thank-you-mail')->first();

        // テンプレートが見つからない場合はエラーを返します。
        if (!$template) {
            return $this->response()->error('メールテンプレートが見つかりません');
        }

        // 注文明細のHTML部分を生成します。
        // メール内で見やすくするために、簡単なインラインスタイルを追加しています。
        $orderItemsHtml = '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px;">';
        $orderItemsHtml .= '<thead><tr>';
        $orderItemsHtml .= '<th style="padding: 8px; border: 1px solid #ddd; text-align: left; background-color: #f2f2f2;">商品ID</th>';
        $orderItemsHtml .= '<th style="padding: 8px; border: 1px solid #ddd; text-align: left; background-color: #f2f2f2;">商品名</th>';
        $orderItemsHtml .= '<th style="padding: 8px; border: 1px solid #ddd; text-align: left; background-color: #f2f2f2;">数量</th>';
        $orderItemsHtml .= '<th style="padding: 8px; border: 1px solid #ddd; text-align: left; background-color: #f2f2f2;">単価</th>';
        $orderItemsHtml .= '<th style="padding: 8px; border: 1px solid #ddd; text-align: left; background-color: #f2f2f2;">小計</th>';
        $orderItemsHtml .= '</tr></thead>';
        $orderItemsHtml .= '<tbody>';

        foreach ($order->orderItems as $item) {
            $orderItemsHtml .= '<tr>';
            $orderItemsHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . $item->product_id . '</td>';
            $orderItemsHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . $item->name . '</td>';
            $orderItemsHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . $item->quantity . '</td>';
            $orderItemsHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . number_format($item->price) . '円</td>';
            $orderItemsHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . number_format($item->subtotal) . '円</td>';
            $orderItemsHtml .= '</tr>';
        }

        $orderItemsHtml .= '</tbody></table>';

        // メールの件名と本文をテンプレートから取得し、デフォルト値を設定します。
        $subject = $template->subject ?? '発送完了のお知らせ';
        $body = $template->body;

        // プレースホルダーを実際のデータに置き換えます。
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
                $orderItemsHtml, // 生成した注文明細HTMLを差し込みます
                number_format($order->shipping_fee) . '円',
                number_format($order->total_amount) . '円',
                'ご利用ありがとうございました。' // フッターの固定メッセージ
            ],
            $body
        );

        // フォームにプレビュー用のフィールドと、送信用の隠しフィールドを追加します。
        // 件名を表示（編集不可）
        $this->text('subject', '件名')
             ->default($subject)
             ->disable();

        // 生のHTMLコンテンツを保持するための隠しtextarea
        // このtextareaはユーザーには見えず、JavaScriptで内容を取得するために使用します。
        $this->textarea('email_raw_html_hidden', 'メール内容プレビュー (HTMLソース)')
             ->default($body)
             ->attribute('id', 'email-raw-html-hidden') // JavaScriptで参照するためのID
             ->attribute('style', 'display:none;') // 完全に非表示にする
             ->disable();

        // レンダリングされたHTMLを表示するためのダミーのtextフィールド
        // このフィールドはJavaScriptによって完全に置き換えられます。
        $this->text('email_rendered_placeholder', 'メール内容プレビュー')
             ->attribute('id', 'email-rendered-placeholder') // JavaScriptで参照するためのID
             ->disable(); // 編集不可にする

        // JavaScriptでダミーフィールドをレンダリングされたHTMLに置き換える
        Admin::script(
<<<SCRIPT
            // モーダルが開かれたときに実行されるように、Bootstrapのイベントリスナーを使用
            $(document).on('shown.bs.modal', function () {
                var rawHtmlTextarea = $('#email-raw-html-hidden'); // 隠しtextareaを取得
                var placeholderElement = $('#email-rendered-placeholder').closest('.form-group'); // ダミーフィールドの親要素を取得

                // 要素が存在することを確認してから操作
                if (rawHtmlTextarea.length && placeholderElement.length) {
                    var rawHtmlContent = rawHtmlTextarea.val(); // 生のHTMLコンテンツを取得

                    // レンダリングされたHTMLを格納する新しいdiv要素を作成
                    var renderedDiv = $('<div/>')
                                        .attr('style', 'border: 1px solid #ddd; padding: 15px; background-color: #f9f9f9; min-height: 200px; overflow-y: auto;')
                                        .html(rawHtmlContent);

                    // ダミーフィールドのform-group全体を新しいdivで置き換える
                    placeholderElement.replaceWith(renderedDiv);
                } else {
                    console.warn('Email preview elements not found or script executed too early.');
                }
            });
SCRIPT
        );

        // 送信用の件名を隠しフィールドで渡します。
        $this->hidden('email_subject')
             ->default($subject);

        // 送信用の本文を隠しフィールドで渡します。
        $this->hidden('email_body')
             ->default($body);
    }

    /**
     * アクションが実行された際にメール送信処理を行います。
     * このメソッドは、プレビューフォームが送信された後に呼び出されます。
     *
     * @param Model $order 現在のモデルインスタンス（注文情報）
     * @param Request $request フォームからのリクエストデータ
     * @return \Encore\Admin\Actions\Response
     */
    public function handle(Model $order, Request $request)
    {
        // メール送信前の最終バリデーションを行います。
        if (empty($order->tracking_number)) {
            return $this->response()->error('配送伝票番号が入力されていません')->refresh();
        }
        if (empty($order->shipping_company)) {
            return $this->response()->error('運送会社が入力されていません')->refresh();
        }
        if (empty($order->shipping_date)) {
            return $this->response()->error('発送日が未入力です')->refresh();
        }

        // 関連する顧客情報を取得します。
        $customer = $order->customer;

        // フォームから送信された件名と本文を取得します。
        $subject = $request->input('email_subject');
        $body = $request->input('email_body');

        // メール送信処理を実行します。
        try {
            Mail::send([], [], function ($message) use ($customer, $subject, $body) {
                $message->to($customer->email) // 顧客のメールアドレス宛に送信
                    ->subject($subject) // 件名を設定
                    ->setBody($body, 'text/html'); // HTMLメールとして本文を設定
            });

            // 送信成功メッセージを返します。
            return $this->response()->success('発送メールを送信しました')->refresh();
        } catch (\Exception $e) {
            return $this->response()->error('送信エラー: ' . $e->getMessage())->refresh();
        }
    }
}
