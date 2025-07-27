<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品発送のお知らせ</title>
    <style>
        body {
            font-family: 'Hiragino Sans', 'Hiragino Kaku Gothic ProN', 'Yu Gothic', 'Meiryo', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            color: #555;
            border-left: 4px solid #007bff;
            padding-left: 10px;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table th,
        .info-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .info-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .product-table th,
        .product-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .product-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .product-table .quantity,
        .product-table .price {
            text-align: center;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .shipping-info {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>商品発送のお知らせ</h1>
        </div>

        <div class="section">
            <p><strong>{{ $order->customer->sei ?? '' }} {{ $order->customer->mei ?? '' }}</strong> 様</p>
            <p>いつもご利用いただき、ありがとうございます。<br>
            ご注文いただきました商品を発送いたしましたので、お知らせいたします。</p>
        </div>

        <div class="section">
            <h3>ご注文情報</h3>
            <table class="info-table">
                <tr>
                    <th>注文番号</th>
                    <td>{{ $order->order_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>注文日時</th>
                    <td>{{ $order->created_at ? $order->created_at->format('Y年m月d日 H:i') : 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>配送情報</h3>
            <div class="shipping-info">
                <table class="info-table">
                    <tr>
                        <th>発送日</th>
                        <td>{{ $order->shipping_date ? \Carbon\Carbon::parse($order->shipping_date)->format('Y年m月d日') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>運送会社</th>
                        <td>{{ $order->shipping_company ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>追跡番号</th>
                        <td><strong>{{ $order->tracking_number ?? 'N/A' }}</strong></td>
                    </tr>
                </table>
                <p><small>※ 追跡番号で配送状況をご確認いただけます。</small></p>
            </div>
        </div>

        <div class="section">
            <h3>ご注文商品</h3>
            @if($order && $order->orderItems && $order->orderItems->count() > 0)
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>商品名</th>
                            <th class="quantity">数量</th>
                            <th class="price">単価</th>
                            <th class="price">小計</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->product_name ?? $item->name ?? 'N/A' }}</td>
                                <td class="quantity">{{ number_format($item->quantity ?? 0) }}</td>
                                <td class="price">¥{{ number_format($item->price ?? 0) }}</td>
                                <td class="price">¥{{ number_format(($item->price ?? 0) * ($item->quantity ?? 0)) }}</td>
                            </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right;"><strong>合計金額</strong></td>
                            <td class="price"><strong>¥{{ number_format($order->total_price ?? 0) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            @else
                <p>商品情報が見つかりません。</p>
            @endif
        </div>

        @if($order->delivery)
        <div class="section">
            <h3>お届け先</h3>
            <table class="info-table">
                <tr>
                    <th>お名前</th>
                    <td>{{ $order->delivery->sei ?? '' }} {{ $order->delivery->mei ?? '' }}</td>
                </tr>
                <tr>
                    <th>住所</th>
                    <td>
                        〒{{ $order->delivery->zip ?? '' }}<br>
                        {{ $order->delivery->input_add01 ?? '' }}{{ $order->delivery->input_add02 ?? '' }}{{ $order->delivery->input_add03 ?? '' }}
                    </td>
                </tr>
                <tr>
                    <th>電話番号</th>
                    <td>{{ $order->delivery->phone ?? '' }}</td>
                </tr>
            </table>
        </div>
        @endif

        <div class="section">
            <p>商品到着まで今しばらくお待ちください。<br>
            ご不明な点がございましたら、お気軽にお問い合わせください。</p>
        </div>

        <div class="footer">
            <p>このメールは自動送信されています。<br>
            返信いただいても対応できませんので、お問い合わせは専用フォームまたはお電話でお願いいたします。</p>
        </div>
    </div>
</body>
</html>