@php
    use App\Models\Order;
    use App\Models\Customer;

    $orders = Order::orderBy('created_at', 'desc')->limit(3)->get();
@endphp

<table class="table table-bordered">
    <thead>
        <tr>
            <th>注文ID</th>
            <th>顧客名</th>
            <th>合計金額</th>
            <th>日時</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->customer->full_name ?? '未登録' }}</td> {{-- フィールド名に応じて調整 --}}
                <td>¥{{ number_format($order->total_price) }}</td> {{-- フィールド名に応じて調整 --}}
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
