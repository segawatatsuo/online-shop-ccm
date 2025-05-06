@extends('layouts.app')

@section('content')
<div class="container">
    <h2>注文履歴</h2>

    @forelse ($orders as $order)
        <div class="card mb-3">
            <div class="card-header">
                注文日: {{ $order->created_at->format('Y年m月d日 H:i') }} / 合計金額: ¥{{ number_format($order->total_price) }}
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($order->orderItems as $item)
                    <li class="list-group-item">
                      {{ $item->product->name ?? '不明な商品' }} - ¥{{ number_format($item->price) }} × {{ $item->quantity }}
                    </li>
                @endforeach
            </ul>
        </div>
    @empty
        <p>まだ注文はありません。</p>
    @endforelse
</div>
@endsection
