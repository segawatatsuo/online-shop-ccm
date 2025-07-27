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

        $template = EmailTemplate::where('slug', 'thank-you-mail')->first();

        if (!$template) {
            return $this->response()->error('メールテンプレートが見つかりません');
        }

        // 件名をテンプレートから
        $subject = $template->subject ?? '発送完了のお知らせ';

        // メール本文を生成
        try {
            $emailBody = view('emails.thank_you', ['order' => $order])->render();
        } catch (\Exception $e) {
            \Log::error('View rendering error: ' . $e->getMessage());
            return $this->response()->error('メールテンプレートの生成に失敗しました: ' . $e->getMessage());
        }

        // 件名表示（編集不可）
        $this->text('subject', '件名')->default($subject)->disable();

        // 隠しフィールド（メール送信時に使用）- data属性も追加
        $this->hidden('order_id')->value($orderId)->attribute('data-order-id', $orderId);
        $this->hidden('email_subject')->value($subject);
        $this->hidden('email_body')->value($emailBody);

        // 送信先メールアドレス表示
        $this->text('recipient', '送信先')->default($customer->email ?? 'N/A')->disable();

        // プレビュー情報をテキストで表示
        $previewInfo = "注文番号: " . ($order->order_number ?? 'N/A') . "\n";
        $previewInfo .= "顧客名: " . ($customer->sei ?? '') . ' ' . ($customer->mei ?? '') . "\n";
        $previewInfo .= "発送日: " . ($order->shipping_date ?? 'N/A') . "\n";
        $previewInfo .= "運送会社: " . ($order->shipping_company ?? 'N/A') . "\n";
        $previewInfo .= "追跡番号: " . ($order->tracking_number ?? 'N/A') . "\n\n";
        $previewInfo .= "商品一覧:\n";
        
        if ($order->orderItems && $order->orderItems->count() > 0) {
            foreach ($order->orderItems as $item) {
                $previewInfo .= "- " . ($item->product_name ?? $item->name ?? 'N/A') . " × " . ($item->quantity ?? 0) . "\n";
            }
        } else {
            $previewInfo .= "商品情報なし\n";
        }
        
        $previewInfo .= "\n合計金額: ¥" . number_format($order->total_price ?? 0);

        $this->textarea('preview_info', 'メール内容プレビュー')->default($previewInfo)->rows(10)->disable();

        // プレビューリンクを表示
        $previewUrl = route('admin.mail-preview', ['orderId' => $orderId]);
        $this->text('preview_link', 'HTMLプレビューリンク')->default($previewUrl)->disable();

        // カスタムJavaScriptでプレビューボタンを追加（モーダル対応版）
        Admin::script("
            $(document).ready(function() {
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
                                var previewUrl = '/admin/mail-preview/' + orderId;
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
     * メール送信処理
     */
    public function handle(Model $model, Request $request)
    {
        // デバッグ: リクエストの全内容をログ出力
        \Log::info('SendShippingMail handle() - All request data: ' . json_encode($request->all()));

        $orderId = $request->input('order_id');

        // デバッグ用ログ
        \Log::info('SendShippingMail handle() - Received Order ID: ' . $orderId);
        \Log::info('SendShippingMail handle() - Model passed: ' . ($model ? get_class($model) . ' ID:' . $model->getKey() : 'null'));

        // リクエストにorder_idがない場合は、渡されたmodelを使用
        if (!$orderId && $model) {
            $orderId = $model->getKey();
            \Log::info('SendShippingMail handle() - Using model ID instead: ' . $orderId);
        }

        if (!$orderId) {
            $debugData = [
                'request_data' => $request->all(),
                'model_exists' => $model ? 'Yes' : 'No',
                'model_class' => $model ? get_class($model) : 'null',
                'model_id' => $model ? $model->getKey() : 'null'
            ];
            return $this->response()->error('注文IDが取得できませんでした。デバッグ: ' . json_encode($debugData, JSON_UNESCAPED_UNICODE))->refresh();
        }

        // 確実にデータを再取得
        $order = Order::with(['customer', 'delivery', 'orderItems'])->find($orderId);

        if (!$order) {
            return $this->response()->error('注文情報が見つかりません（ID: ' . $orderId . '）')->refresh();
        }

        // デバッグ用ログ
        \Log::info('SendShippingMail handle() - Found order: ' . $order->order_number);

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

        $subject = $request->input('email_subject');
        $body = $request->input('email_body');

        // メール本文が空の場合は再生成
        if (empty($body)) {
            try {
                $body = view('emails.thank_you', ['order' => $order])->render();
                \Log::info('SendShippingMail handle() - Regenerated email body');
            } catch (\Exception $e) {
                \Log::error('SendShippingMail handle() - View rendering error: ' . $e->getMessage());
                return $this->response()->error('メールテンプレートの生成に失敗しました: ' . $e->getMessage())->refresh();
            }
        }

        // メール送信
        try {
            Mail::send([], [], function ($message) use ($customer, $subject, $body) {
                $message->to($customer->email)
                    ->subject($subject)
                    ->setBody($body, 'text/html');
            });

            return $this->response()->success('発送メールを送信しました（注文: ' . $order->order_number . '）')->refresh();
        } catch (\Exception $e) {
            \Log::error('Mail sending error: ' . $e->getMessage());
            return $this->response()->error('送信エラー: ' . $e->getMessage())->refresh();
        }
    }
}