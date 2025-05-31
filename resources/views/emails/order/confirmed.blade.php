@component('mail::message')
# CCメディコです。ご注文ありがとうございます

{{ $order->customer->sei }} {{ $order->customer->mei }} 様

以下の内容でご注文を承りました。

---

## ■ 注文情報

- 注文番号：{{ $order->order_number }}
- 注文日時：{{ $order->created_at->format('Y年m月d日 H:i') }}

---

## ■ ご注文内容

<table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; text-align: left;">
    <thead>
        <tr>
            <th align="left" style="width: 45%;">商品名</th>
            <th align="left" style="width: 15%;">単価</th>
            <th align="left" style="width: 10%;">数量</th>
            <th align="left" style="width: 20%;">小計</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($order->orderItems as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td>￥{{ number_format($item->price) }}</td>
            <td>{{ $item->quantity }}</td>
            <td>￥{{ number_format($item->price * $item->quantity) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

---

- 商品合計：￥{{ number_format($order->total_price) }}  
- 送料：￥{{ number_format($order->shipping_fee ?? 0) }}  
- 合計金額：￥{{ number_format($order->total_price + ($order->shipping_fee ?? 0)) }}

---

## ■ お届け先

- お名前：{{ $delivery->sei }} {{ $delivery->mei }} 様  
- 郵便番号：{{ $delivery->zip }}  
- 住所：{{ $delivery->input_add01 }} {{ $delivery->input_add02 }} {{ $delivery->input_add03 }}  
- 電話番号：{{ $delivery->phone }}

---

ご不明な点がございましたら、お気軽にお問い合わせください。  
今後ともCCメディコをよろしくお願いいたします。

@endcomponent
