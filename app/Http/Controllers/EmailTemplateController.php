<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('slug')->get();
        return view('admin.email_templates.index', compact('templates'));
    }

    public function show($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return view('admin.email_templates.show', compact('template'));
    }

    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);
        return view('admin.email_templates.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $template = EmailTemplate::findOrFail($id);
        $template->update([
            'subject' => $request->subject,
            'body' => $request->body,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.email-templates.index')
                        ->with('success', 'メールテンプレートを更新しました。');
    }

    public function preview($id)
    {
        $template = EmailTemplate::findOrFail($id);
        
        // プレビュー用のサンプルデータ
        $sampleData = [
            'customer_name' => '山田太郎',
            'order_id' => '12345',
            'order_number' => 'ORD-2024-001',
            'order_date' => '2024年7月29日 14:30',
            'order_items_table' => '| サンプル商品A | ¥1,000 | 2 | ¥2,000 |' . "\n" . '| サンプル商品B | ¥1,500 | 1 | ¥1,500 |',
            'total_price' => '3,500',
            'shipping_fee' => '500',
            'grand_total' => '4,000',
            'delivery_name' => '山田太郎',
            'delivery_postal_code' => '123-4567',
            'delivery_address' => '東京都渋谷区サンプル1-2-3',
            'delivery_phone' => '03-1234-5678',
        ];

        $previewContent = $template->render($sampleData);
        
        return view('admin.email_templates.preview', compact('template', 'previewContent'));
    }
}