@component('mail::message')
# 新しい注文が入りました

注文番号：{{ $order->order_number }}<br>
顧客名：{{ $customer->name }}<br>
合計金額：¥{{ number_format($order->total_price) }}

@component('mail::table')
| 商品名 | 数量 | 金額 |
|--------|------|------|
@foreach ($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | ¥{{ number_format($item->price) }} |
@endforeach
@endcomponent

@endcomponent
