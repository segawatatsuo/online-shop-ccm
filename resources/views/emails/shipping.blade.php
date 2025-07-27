<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>発送完了のお知らせ</title>
</head>
<body>
    <p>{{ $order->customer->full_name }} 様</p>
    <p>ご注文ありがとうございます。</p>
    <p>以下の内容で商品を発送いたしました。</p>
    <hr>
    <p>注文番号: {{ $order->order_number }}</p>
    <p>発送日: {{ $order->shipping_date }}</p>
    <p>配送会社: {{ $order->shipping_company }}</p>
    <p>伝票番号: {{ $order->tracking_number }}</p>
    <hr>
    {!! $orderItemsHtml !!}
    <hr>
    <p>送料: {{ number_format($order->shipping_fee) }}円</p>
    <p>合計: {{ number_format($order->total_amount) }}円</p>
    <p>またのご利用をお待ちしております。</p>
</body>
</html>
