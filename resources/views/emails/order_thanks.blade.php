@component('mail::message')
# ご注文ありがとうございます！

{{ $order->customer->name }} 様

以下の内容でご注文を承りました。

---

## ■ 注文情報

- 注文番号：{{ $order->id }}
- 注文日時：{{ $order->created_at->format('Y年m月d日 H:i') }}

---

## ■ ご注文内容

| 商品名 | 単価 | 数量 | 小計 |
|--------|------|------|------|
@foreach ($order->orderItems as $item)
| {{ $item->product->name }} | ¥{{ number_format($item->price) }} | {{ $item->quantity }} | ¥{{ number_format($item->subtotal) }} |
@endforeach

---

- 商品合計：¥{{ number_format($order->total_price) }}
- 送料：¥{{ number_format($order->shipping_fee ?? 0) }}
- 合計金額：¥{{ number_format($order->total_price + ($order->shipping_fee ?? 0)) }}

---

## ■ お届け先

- お名前：{{ $order->customer->name }}
- 郵便番号：{{ $order->customer->postal_code }}
- 住所：{{ $order->customer->address }}
- 電話番号：{{ $order->customer->phone }}

---

ご不明な点がございましたら、お気軽にお問い合わせください。

今後ともCCメディコをよろしくお願いいたします。

@endcomponent
