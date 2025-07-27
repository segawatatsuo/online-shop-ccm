<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class MailPreviewController extends Controller
{
    /**
     * 従来のビューベースプレビュー
     */
    public function preview($orderId)
    {
        try {
            \Log::info('MailPreviewController - Requested Order ID: ' . $orderId);
            
            $order = Order::with(['customer', 'delivery', 'orderItems'])->findOrFail($orderId);
            
            \Log::info('MailPreviewController - Order found: ' . $order->order_number);

            if (!view()->exists('emails.thank_you')) {
                \Log::error('MailPreviewController - View emails.thank_you not found');
                return response('メールテンプレートが見つかりません', 404);
            }

            $html = view('emails.thank_you', ['order' => $order])->render();
            
            return response()->make(
                $html,
                200,
                [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]
            );
            
        } catch (\Exception $e) {
            \Log::error('MailPreviewController - Error: ' . $e->getMessage());
            
            return response()->make(
                '<html><body><h1>エラーが発生しました</h1><p>' . htmlspecialchars($e->getMessage()) . '</p></body></html>',
                500,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }
    }

    /**
     * テンプレートベースプレビュー
     */
    public function previewTemplate($orderId)
    {
        try {
            \Log::info('MailPreviewController - Template preview requested for Order ID: ' . $orderId);
            
            $order = Order::with(['customer', 'delivery', 'orderItems'])->findOrFail($orderId);
            
            // 発送メール用のテンプレートを取得
            $template = EmailTemplate::where('slug', 'shipping-mail')->first();
            
            if (!$template) {
                return response()->make(
                    '<html><body><h1>テンプレートエラー</h1><p>発送メールテンプレート（slug: shipping-mail）が見つかりません</p></body></html>',
                    404,
                    ['Content-Type' => 'text/html; charset=UTF-8']
                );
            }

            // プレースホルダーを置換してHTMLを生成
            $html = $this->replacePlaceholders($template->body, $order);
            
            \Log::info('MailPreviewController - Template preview generated, HTML length: ' . strlen($html));
            
            return response()->make(
                $html,
                200,
                [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]
            );
            
        } catch (\Exception $e) {
            \Log::error('MailPreviewController - Template preview error: ' . $e->getMessage());
            
            return response()->make(
                '<html><body><h1>プレビューエラー</h1><p>' . htmlspecialchars($e->getMessage()) . '</p></body></html>',
                500,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }
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
            '{{product_list}}' => $this->getProductListHtml($order->orderItems),
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }

    /**
     * 配送先住所を整形
     */
    private function getDeliveryAddress($delivery)
    {
        if (!$delivery) return '';
        
        return '〒' . ($delivery->zip ?? '') . '<br>' . 
               ($delivery->input_add01 ?? '') . 
               ($delivery->input_add02 ?? '') . 
               ($delivery->input_add03 ?? '');
    }

    /**
     * 商品リストをHTML形式で取得
     */
    private function getProductListHtml($orderItems)
    {
        if (!$orderItems || $orderItems->count() === 0) {
            return '<p>商品情報なし</p>';
        }

        $html = '<table style="width: 100%; border-collapse: collapse; margin: 10px 0;">';
        $html .= '<thead><tr style="background-color: #f8f9fa;">';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">商品名</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">数量</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">単価</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">小計</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($orderItems as $item) {
            $html .= '<tr>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . htmlspecialchars($item->product_name ?? $item->name ?? 'N/A') . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . number_format($item->quantity ?? 0) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">¥' . number_format($item->price ?? 0) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">¥' . number_format(($item->price ?? 0) * ($item->quantity ?? 0)) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }
}