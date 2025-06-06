@extends('layouts.app') 

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/address_input.css') }}">
    <link rel="stylesheet" href="{{ asset('css/confirm.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endpush

@section('content')

<main class="main">
<div class="container">
    <h2 class="mb-4">注文内容</h2>

    {{-- ご注文者情報 --}}
    <div class="card mb-4">
        <div class="card-header">ご注文者情報</div>
        <div class="card-body grid-2">
            @php $address = session('address'); @endphp
            <div><strong>氏名：</strong> {{ $address['order_sei'] }} {{ $address['order_mei'] }}</div>
            <div><strong>メール：</strong> {{ $address['order_email'] }}</div>
            <div><strong>電話番号：</strong> {{ $address['order_phone'] }}</div>
            <div><strong>郵便番号：</strong> {{ $address['order_zip'] }}</div>
            <div class="grid-full"><strong>住所：</strong> {{ $address['order_add01'] }} {{ $address['order_add02'] }} {{ $address['order_add03'] }}</div>
        </div>
    </div>

    {{-- お届け先情報 --}}
    <div class="card mb-4">
        <div class="card-header">お届け先情報</div>
        <div class="card-body grid-2">
            <div><strong>氏名：</strong> {{ $address['delivery_sei'] }} {{ $address['delivery_mei'] }}</div>
            <div><strong>メール：</strong> {{ $address['delivery_email'] }}</div>
            <div><strong>電話番号：</strong> {{ $address['delivery_phone'] }}</div>
            <div><strong>郵便番号：</strong> {{ $address['delivery_zip'] }}</div>
            <div class="grid-full"><strong>住所：</strong> {{ $address['delivery_add01'] }} {{ $address['delivery_add02'] }} {{ $address['delivery_add03'] }}</div>
        </div>
    </div>


    {{-- 配送情報 --}}
    <div class="card mb-4">
        <div class="card-header">配送情報</div>
        <div class="card-body grid-2">
            <div><strong>配送希望日：</strong> {{ $address['delivery_date'] }} {{ $address['delivery_date'] }}</div>
            <div><strong>配送時間：</strong> {{ $address['delivery_time'] }}</div>
            <div><strong>ご要望他：</strong> {{ $address['your_request'] }}</div>
        </div>
    </div>


    {{-- 商品情報 --}}
    <div class="card mb-4">
        <div class="card-header">ご注文商品</div>
        <div class="card-body">
            @php
                $cart = session('cart', []);
                $total = 0;
            @endphp
            <table>
                <thead>
                    <tr>
                        
                        <th>商品名</th>
                        <th>数量</th>
                        <th>小計</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        @endphp
                        <tr>
                            
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ number_format($subtotal) }}円</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" style="text-align:right;">合計金額</th>
                        <th>{{ number_format($total) }}円</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ボタン --}}
    {{--
    <div class="text-center mt-4">
        <a href="{{ route('cart.index') }}" class="btn btn-secondary">戻る</a>
        <form action="{{ route('order.hoge') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-order-confirm">お支払い</button>
        </form>
    </div>
--}}
    {{-- ボタン --}}
    <div class="text-center mt-4">
        <a href="{{ route('cart.index') }}" class="btn btn-secondary">戻る</a>
        <form action="{{ route('cart.square-payment') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-order-confirm">お支払い</button>
        </form>
    </div>


</div>
</main>
@endsection
