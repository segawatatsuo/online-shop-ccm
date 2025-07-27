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
use Encore\Admin\Admin;

class SendShippingMail extends RowAction
{
    public $name = '発送メール送信';

    protected $currentOrderId = null;

    public function __construct($row = null)
    {
        parent::__construct();
        if ($row) {
            $this->currentOrderId = $row->getKey();
            \Log::info('Constructor - Order ID set: ' . $this->currentOrderId);
        }
    }

    /**
     * プレビュー表示のためのフォームを定義します。
     */
    public function form()
    {
        $orderId = $this->row->id ?? null;

        if (!$orderId) {
            return $this->response()->error('注文IDが取得できません。');
        }

        $order = Order::with(['customer', 'delivery', 'orderItems'])->find($orderId);

        if (!$order) {
            return $this->response()->error('注文情報が見つかりません（ID: ' . $orderId . '）');
        }

        if (empty($order->tracking_number) || empty($order->shipping_company) || empty($order->shipping_date)) {
            return $this->response()->error('配送情報が不足しています');
        }

        $customer = $order->customer;
        $delivery = $order->delivery;

        if (!$customer || !$delivery) {
            return $this->response()->error('顧客または配送先情報が見つかりません');
        }

        // 発送メール用のテンプレートを取得
        $template = EmailTemplate::where('slug', 'shipping-mail')->first();

        if (!$template) {
            return $this->response()->error('発送メールテンプレートが見つかりません（slug: shipping-mail）');
        }

        // テンプレートから件名と本文を取得
        $subject = $this->replacePlaceholders($template->subject, $order);
        $bodyTemplate = $template->body;

        // メール本文を生成（プレースホルダーを実際の値に置換）
        $emailBody = $this->generateEmailBody($bodyTemplate, $order);

        // 件名表示（編集可能）
        $this->text('subject', '件名')->default($subject);

        // メール本文表示（編集可能）
        $this->textarea('email_body_template', 'メール本文テンプレート')->default($bodyTemplate)->rows(15);

        // 隠しフィールド（メール送信時に使用）- data属性も追加
        $this->hidden('order_id')->value($orderId)->attribute('data-order-id', $orderId);
        $this->hidden('template_id')->value($template->id);

        // 送信先メールアドレス表示
        $this->text('recipient', '送信先')->default($customer->email ?? 'N/A')->disable();

        // プレースホルダー説明
        $placeholderInfo = $this->getPlaceholderInfo();
        $this->textarea('placeholder_info', '使用可能なプレースホルダー')->default($placeholderInfo)->rows(8)->disable();

        // プレビュー情報をテキストで表示
        $previewInfo = $this->generatePreviewText($order, $emailBody);
        $this->textarea('preview_info', 'メール内容プレビュー')->default($previewInfo)->rows(10)->disable();

        // プレビューリンクを表示（正しいURLで）
        $previewUrl = url('admin/mail-preview/' . $orderId);
        $this->text('preview_link', 'HTMLプレビューリンク')->default($previewUrl)->disable();

        // カスタムJavaScriptでプレビューボタンを追加（サーバー環境対応版）
        Admin::script("
            $(document).ready(function() {
                // ベースURLを取得（Laravel環境に応じて自動設定）
                var baseUrl = '" . url('/') . "';
                
                // モーダルが開かれた時に実行
                $(document).on('shown.bs.modal', '.modal', function() {
                    var modal = $(this);
                    // このモーダル内にプレビューボタンがあるかチェック
                    if (modal.find('.preview-btn').length === 0) {
                        modal.find('.modal-footer .btn-primary').before('<button type=\"button\" class=\"btn btn-info preview-btn\" style=\"margin-right: 10px;\">HTMLプレビューを開く</button>');
                        
                        modal.find('.preview-btn').click(function() {
                            // 現在のモーダル内のorder_idを取得
                            var orderId = modal.find('input[name=\"order_id\"]').val();
                            console.log('Preview button clicked, Modal Order ID:', orderId);
                            
                            // order_idが取得できない場合の代替手段
                            if (!orderId) {
                                // データ属性から取得を試す
                                orderId = modal.find('input[data-order-id]').attr('data-order-id');
                                console.log('Fallback: trying data-order-id:', orderId);
                            }
                            
                            if (orderId) {
                                var previewUrl = baseUrl + '/admin/mail-preview-template/' + orderId;
                                console.log('Opening preview URL:', previewUrl);
                                window.open(previewUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
                            } else {
                                console.error('Order ID not found in modal');
                                console.log('Available inputs:', modal.find('input').map(function() { return this.name + '=' + this.value; }).get());
                                alert('注文IDが取得できません');
                            }
                        });
                    }
                });
            });
        ");
    }

    /**
     * プレースホルダーを実際の値に置換
     */
    private function replacePlaceholders($text, $order)
    {
        $customer = $order->customer;
        $delivery = $order->delivery;

        $placeholders = [
            '{{customer_name}}' => ($customer->sei ?? '') . ' ' . ($customer->mei ?? ''),
            '{{customer_sei}}' => $customer->sei ?? '',
            '{{customer_mei}}' => $customer->mei ?? '',
            '{{order_number}}' => $order->order_number ?? '',
            '{{order_date}}' => $order->created_at ? $order->created_at->format('Y年m月d日') : '',
            '{{shipping_date}}' => $order->shipping_date ? \Carbon\Carbon::parse($order->shipping_date)->format('Y年m月d日') : '',
            '{{shipping_company}}' => $order->shipping_company ?? '',
            '{{tracking_number}}' => $order->tracking_number ?? '',
            '{{delivery_address}}' => $this->getDeliveryAddress($delivery),
            '{{delivery_name}}' => ($delivery->sei ?? '') . ' ' . ($delivery->mei ?? ''),
            '{{total_price}}' => '¥' . number_format($order->total_price ?? 0),
            '{{product_list}}' => $this->getProductListText($order->orderItems),
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }

    /**
     * メール本文を生成
     */
    private function generateEmailBody($template, $order)
    {
        return $this->replacePlaceholders($template, $order);
    }

    /**
     * 配送先住所を整形
     */
    private function getDeliveryAddress($delivery)
    {
        if (!$delivery) return '';
        
        return '〒' . ($delivery->zip ?? '') . ' ' . 
               ($delivery->input_add01 ?? '') . 
               ($delivery->input_add02 ?? '') . 
               ($delivery->input_add03 ?? '');
    }

    /**
     * 商品リストをテキスト形式で取得
     */
    private function getProductListText($orderItems)
    {
        if (!$orderItems || $orderItems->count() === 0) {
            return '商品情報なし';
        }

        $productList = '';
        foreach ($orderItems as $item) {
            $productList .= '・' . ($item->product_name ?? $item->name ?? 'N/A') . 
                           ' × ' . ($item->quantity ?? 0) . 
                           ' (¥' . number_format(($item->price ?? 0) * ($item->quantity ?? 0)) . ')' . "\n";
        }

        return rtrim($productList, "\n");
    }

    /**
     * プレースホルダー情報を取得
     */
    private function getPlaceholderInfo()
    {
        return "使用可能なプレースホルダー:\n\n" .
               "{{customer_name}} - 顧客氏名（姓 名）\n" .
               "{{customer_sei}} - 顧客姓\n" .
               "{{customer_mei}} - 顧客名\n" .
               "{{order_number}} - 注文番号\n" .
               "{{order_date}} - 注文日\n" .
               "{{shipping_date}} - 発送日\n" .
               "{{shipping_company}} - 運送会社\n" .
               "{{tracking_number}} - 追跡番号\n" .
               "{{delivery_address}} - 配送先住所\n" .
               "{{delivery_name}} - 配送先氏名\n" .
               "{{total_price}} - 合計金額\n" .
               "{{product_list}} - 商品リスト";
    }

    /**
     * プレビューテキストを生成
     */
    private function generatePreviewText($order, $emailBody)
    {
        $previewInfo = "=== メール内容プレビュー ===\n\n";
        $previewInfo .= strip_tags($emailBody);
        return $previewInfo;
    }

    /**
     * メール送信処理
     */
    public function handle(Model $model, Request $request)
    {
        // デバッグ: リクエストの全内容をログ出力
        \Log::info('SendShippingMail handle() - All request data: ' . json_encode($request->all()));

        $orderId = $request->input('order_id');

        // リクエストにorder_idがない場合は、渡されたmodelを使用
        if (!$orderId && $model) {
            $orderId = $model->getKey();
            \Log::info('SendShippingMail handle() - Using model ID instead: ' . $orderId);
        }

        if (!$orderId) {
            return $this->response()->error('注文IDが取得できませんでした。')->refresh();
        }

        // 確実にデータを再取得
        $order = Order::with(['customer', 'delivery', 'orderItems'])->find($orderId);

        if (!$order) {
            return $this->response()->error('注文情報が見つかりません（ID: ' . $orderId . '）')->refresh();
        }

        // バリデーション
        if (empty($order->tracking_number)) {
            return $this->response()->error('配送伝票番号が入力されていません')->refresh();
        }
        if (empty($order->shipping_company)) {
            return $this->response()->error('運送会社が入力されていません')->refresh();
        }
        if (empty($order->shipping_date)) {
            return $this->response()->error('発送日が未入力です')->refresh();
        }

        $customer = $order->customer;
        if (!$customer) {
            return $this->response()->error('顧客情報が見つかりません')->refresh();
        }

        // フォームから件名とテンプレートを取得
        $subject = $request->input('subject');
        $bodyTemplate = $request->input('email_body_template');

        // 件名のプレースホルダーを置換
        $finalSubject = $this->replacePlaceholders($subject, $order);

        // 本文のプレースホルダーを置換
        $finalBody = $this->replacePlaceholders($bodyTemplate, $order);

        // メール送信
        try {
            Mail::send([], [], function ($message) use ($customer, $finalSubject, $finalBody) {
                $message->to($customer->email)
                    ->subject($finalSubject)
                    ->setBody($finalBody, 'text/html');
            });

            return $this->response()->success('発送メールを送信しました（注文: ' . $order->order_number . '）')->refresh();
        } catch (\Exception $e) {
            \Log::error('Mail sending error: ' . $e->getMessage());
            return $this->response()->error('送信エラー: ' . $e->getMessage())->refresh();
        }
    }
}