<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class MailPreviewController extends Controller
{
    public function preview($orderId)
    {
        try {
            // デバッグログ
            \Log::info('MailPreviewController - Requested Order ID: ' . $orderId);
            
            $order = Order::with(['customer', 'delivery', 'orderItems'])->findOrFail($orderId);
            
            // デバッグログ
            \Log::info('MailPreviewController - Order found: ' . $order->order_number);
            \Log::info('MailPreviewController - Customer: ' . ($order->customer ? $order->customer->sei . ' ' . $order->customer->mei : 'not found'));
            \Log::info('MailPreviewController - Order items count: ' . $order->orderItems->count());

            // ビューが存在するかチェック
            if (!view()->exists('emails.thank_you')) {
                \Log::error('MailPreviewController - View emails.thank_you not found');
                return response('メールテンプレートが見つかりません', 404);
            }

            $html = view('emails.thank_you', ['order' => $order])->render();
            
            // デバッグ用：生成されたHTMLの長さをログ出力
            \Log::info('MailPreviewController - Generated HTML length: ' . strlen($html));
            
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
            \Log::error('MailPreviewController - Stack trace: ' . $e->getTraceAsString());
            
            return response()->make(
                '<html><body><h1>エラーが発生しました</h1><p>' . htmlspecialchars($e->getMessage()) . '</p></body></html>',
                500,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }
    }
}