<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\CompanyInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $customer;
    public $delivery;
    public $subject;
    public $body;
    public $companyCcEmail;


    /**
     * 新しいメッセージインスタンスを作成します。
     */
    public function __construct($order, $customer, $delivery)
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->delivery = $delivery;

        try {
            $this->loadEmailTemplate();
            $this->loadCompanyInfo();
            $this->replacePlaceholders();
        } catch (\Exception $e) {
            Log::error('OrderConfirmed Mailable初期化エラー', [
                'error' => $e->getMessage(),
                'order_id' => $order->id ?? 'unknown'
            ]);
            throw $e;
        }
    }


    /**
     * メールテンプレートを読み込む
     */
    protected function loadEmailTemplate()
    {
        $template = EmailTemplate::where('slug', 'order-info')->first();
        
        if ($template) {
            $this->subject = $template->subject;
            $this->body = $template->body;
        } else {
            Log::warning('メールテンプレートが見つかりません', ['slug' => 'order-info']);
            $this->subject = 'ご注文確認メール';
            $this->body = $this->getDefaultTemplate();
        }
    }

    /**
     * 会社情報を読み込む
     */
    protected function loadCompanyInfo()
    {
        $companyCc = CompanyInfo::where('key', 'company-mail')->first();
        $this->companyCcEmail = $companyCc ? $companyCc->value : null;
    }


    /**
     * メッセージを構築します。
     *
     * @return $this
     */
    public function build()
    {
        try {
            // Bladeテンプレートの存在確認
            if (!view()->exists('emails.dynamic_mail_layout')) {
                Log::error('メールテンプレートファイルが存在しません', [
                    'template' => 'emails.dynamic_mail_layout'
                ]);
                throw new \Exception('メールテンプレートファイルが見つかりません');
            }

            $mail = $this->subject($this->subject)
                ->view('emails.dynamic_mail_layout');

            if ($this->companyCcEmail) {
                $mail->cc($this->companyCcEmail);
            }

            return $mail;
        } catch (\Exception $e) {
            Log::error('メール構築エラー', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * 本文のプレースホルダーを実際のデータに置換する
     */
    protected function replacePlaceholders()
    {
        try {
            // 顧客情報の安全な取得
            $customerName = $this->getCustomerName();
            $customerAddress = $this->getCustomerAddress();
            $customerZip = $this->getCustomerZip();
            $customerPhone = $this->getCustomerPhone();

            $deliveryName = $this->getDeliveryName();
            $deliveryAddress = $this->getDeliveryAddress();
            $deliveryZip = $this->getDeliveryZip();
            $deliveryPhone = $this->getDeliveryPhone();
            
            $this->body = str_replace('{{customer_name}}', $customerName, $this->body);//顧客名
            $this->body = str_replace('{{customer_address}}', $customerAddress, $this->body);//顧客住所
            $this->body = str_replace('{{customer_zip}}', $customerZip, $this->body);//郵便番号
            $this->body = str_replace('{{customer_phone}}', $customerPhone, $this->body);//電話番号

            // 配送先情報
            $this->body = str_replace('{{delivery_name}}', $deliveryName, $this->body);//配送先名
            $this->body = str_replace('{{delivery_address}}', $deliveryAddress, $this->body);//配送先住所
            $this->body = str_replace('{{delivery_zip}}', $deliveryZip, $this->body);//郵便番号
            $this->body = str_replace('{{delivery_phone}}', $deliveryPhone, $this->body);//電話番号

            // 注文情報
            $this->body = str_replace('{{order_number}}', $this->order->order_number ?? '不明', $this->body);//注文番号
            $this->body = str_replace('{{order_date}}', 
                $this->order->created_at ? $this->order->created_at->format('Y年m月d日 H時i分') : '不明', 
                $this->body
            );//注文日
            $this->body = str_replace('{{total_price}}', $this->order->total_price, $this->body);//合計
            $this->body = str_replace('{{shipping}}', $this->order->shipping ?? '0', $this->body);//送料

            // 注文商品の情報
            $itemsHtml = $this->generateOrderItemsHtml();
            $this->body = str_replace('{{order_items}}', $itemsHtml, $this->body);

        } catch (\Exception $e) {
            Log::error('プレースホルダー置換エラー', [
                'error' => $e->getMessage(),
                'order_id' => $this->order->id ?? 'unknown'
            ]);
            throw $e;
        }
    }

    /**
     * 顧客名を安全に取得
     */
    protected function getCustomerName()
    {
        if (!$this->order || !$this->order->customer) {
            Log::warning('顧客情報が見つかりません', ['order_id' => $this->order->id ?? 'unknown']);
            return 'お客様';
        }

        $sei = $this->order->customer->sei ?? '';
        $mei = $this->order->customer->mei ?? '';
        
        return trim($sei . ' ' . $mei) ?: 'お客様';
    }

    /**
     * 顧客住所を安全に取得
     */
    protected function getCustomerAddress()
    {
        if (!$this->order || !$this->order->customer) {
            return '';
        }

        $add01 = $this->order->customer->input_add01 ?? '';
        $add02 = $this->order->customer->input_add02 ?? '';
        $add03 = $this->order->customer->input_add03 ?? '';
        
        return trim($add01 . ' ' . $add02 . ' ' . $add03);
    }


    /**
     * 顧客郵便番号を安全に取得
     */
    protected function getCustomerZip()
    {
        if (!$this->order || !$this->order->customer) {
            return '';
        }

        $zip = $this->order->customer->zip ?? '';
        
        return trim($zip);
    }
    /**
     * 顧客郵便番号を安全に取得
     */
    protected function getCustomerPhone()
    {
        if (!$this->order || !$this->order->customer) {
            return '';
        }

        $phone = $this->order->customer->phone ?? '';
        
        return trim($phone);
    }



    /**
     * 送付先名を安全に取得
     */
    protected function getDeliveryName()
    {
        if (!$this->order || !$this->order->delivery) {
            Log::warning('送付先情報が見つかりません', ['order_id' => $this->order->id ?? 'unknown']);
            return 'お届け先';
        }

        $sei = $this->order->delivery->sei ?? '';
        $mei = $this->order->delivery->mei ?? '';
        
        return trim($sei . ' ' . $mei) ?: 'お届け先';
    }

    /**
     * 送付先住所を安全に取得
     */
    protected function getDeliveryAddress()
    {
        if (!$this->order || !$this->order->delivery) {
            return '';
        }

        $add01 = $this->order->delivery->input_add01 ?? '';
        $add02 = $this->order->delivery->input_add02 ?? '';
        $add03 = $this->order->delivery->input_add03 ?? '';
        
        return trim($add01 . ' ' . $add02 . ' ' . $add03);
    }

    /**
     * 送付先郵便番号を安全に取得
     */
    protected function getDeliveryZip()
    {
        if (!$this->order || !$this->order->delivery) {
            return '';
        }

        $zip = $this->order->delivery->zip ?? '';
        
        return trim($zip);
    }
    /**
     * 送付先電話番号を安全に取得
     */
    protected function getDeliveryPhone()
    {
        if (!$this->order || !$this->order->delivery) {
            return '';
        }

        $phone = $this->order->delivery->phone ?? '';
        
        return trim($phone);
    }

    /**
     * 注文商品のHTMLを生成
     */
    protected function generateOrderItemsHtml()
    {
        if (!$this->order || !$this->order->orderItems) {
            return '<tr><td colspan="5">商品情報が見つかりません</td></tr>';
        }

        $itemsHtml = '<table style="border: 1px solid;"><tr><th style="border: 1px solid";>商品コード</th><th style="border: 1px solid";>商品名</th><th style="border: 1px solid";>価格</th><th style="border: 1px solid";>数量</th><th style="border: 1px solid";>小計</th></tr>';

        foreach ($this->order->orderItems as $item) {
            $itemsHtml .= "<tr>";
            $itemsHtml .= "<td style='border: 1px solid;'>" . htmlspecialchars($item->product_code ?? '') . "</td>";
            $itemsHtml .= "<td style='border: 1px solid;'>" . htmlspecialchars($item->name ?? '') . "</td>";
            $itemsHtml .= "<td style='border: 1px solid;'>" . number_format($item->price ?? 0) . "円</td>";
            $itemsHtml .= "<td style='border: 1px solid;'>" . ($item->quantity ?? 0) . "</td>";
            $itemsHtml .= "<td style='border: 1px solid;'>" . number_format($item->subtotal ?? 0) . "円</td>";
            $itemsHtml .= "</tr>";
        }

        $itemsHtml .= '</table>';

        return $itemsHtml;
    }

    /**
     * デフォルトのメールテンプレートを返す
     */
    protected function getDefaultTemplate()
    {
        return '
            <h2>ご注文ありがとうございます</h2>
            <p>{{ customer_name }} 様</p>
            <p>この度はご注文いただきありがとうございます。</p>
            
            <h3>ご注文内容</h3>
            <p>注文番号: {{ order_number }}</p>
            <p>注文日時: {{ order_date }}</p>
            
            <table border="1">
                <thead>
                    <tr>
                        <th>商品コード</th>
                        <th>商品名</th>
                        <th>単価</th>
                        <th>数量</th>
                        <th>小計</th>
                    </tr>
                </thead>
                <tbody>
                    {{ order_items }}
                </tbody>
            </table>
        ';
    }
}