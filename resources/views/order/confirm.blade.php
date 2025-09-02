@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/kakunin-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">

    <style>
        .order-table tfoot tr th,
        .order-table tfoot tr td {
            border-top: 1px solid #ccc;
            /* 合計金額セクションの上に線を入れる */
        }

        /* 合計金額の行が複数ある場合、最後の行だけ下線を消すことも検討 */
        .order-table tfoot tr:last-child th,
        .order-table tfoot tr:last-child td {
            border-bottom: none;
        }
    </style>




@endpush

@section('content')

    <main class="main">
        <div class="container">
            <h2 class="section-title">ご注文内容の確認</h2>

            <div class="info-card">
                <div class="card-header">
                    <h3>ご注文者情報</h3>
                </div>
                <div class="card-body grid-layout">
                    @php $address = session('address'); @endphp
                    <div><strong>氏名:</strong> {{ $address['order_sei'] }} {{ $address['order_mei'] }}</div>
                    <div><strong>メール:</strong> {{ $address['order_email'] }}</div>
                    <div><strong>電話番号:</strong> {{ $address['order_phone'] }}</div>
                    <div><strong>郵便番号:</strong> {{ $address['order_zip'] }}</div>
                    <div class="grid-full"><strong>住所:</strong> {{ $address['order_add01'] }} {{ $address['order_add02'] }}
                        {{ $address['order_add03'] }}</div>
                </div>
            </div>

            <div class="info-card">
                <div class="card-header">
                    <h3>お届け先情報</h3>
                </div>
                <div class="card-body grid-layout">
                    <div><strong>氏名:</strong> {{ $address['delivery_sei'] }} {{ $address['delivery_mei'] }}</div>
                    <div><strong>メール:</strong> {{ $address['delivery_email'] }}</div>
                    <div><strong>電話番号:</strong> {{ $address['delivery_phone'] }}</div>
                    <div><strong>郵便番号:</strong> {{ $address['delivery_zip'] }}</div>
                    <div class="grid-full"><strong>住所:</strong> {{ $address['delivery_add01'] }}
                        {{ $address['delivery_add02'] }} {{ $address['delivery_add03'] }}</div>
                </div>
            </div>

            <div class="info-card">
                <div class="card-header">
                    <h3>配送情報</h3>
                </div>
                <div class="card-body grid-layout">
                    <div><strong>配送希望日:</strong> {{ $address['delivery_date'] }}</div>
                    <div><strong>配送時間:</strong> {{ $address['delivery_time'] }}</div>
                    <div class="grid-full"><strong>ご要望他:</strong> {{ $address['your_request'] }}</div>
                </div>
            </div>

            <div class="info-card">
                <div class="card-header">
                    <h3>ご注文商品</h3>
                </div>
                <div class="card-body">
                    @php
                        $cart = session('cart', []);
                        $total = 0;
                    @endphp
                    <div class="table-responsive">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>商品名</th>
                                    <th class="text-center">数量</th>
                                    <th class="text-right">小計</th>
                                </tr>
                            </thead>
                            {{--
                            <tbody>
                                <tr>
                                    <td>エアーストッキングプレミアムシルク 120G テラコッタ</td>
                                    <td class="text-center">1</td>
                                    <td class="text-right">3,300円</td>
                                </tr>
                            </tbody>
                            --}}
                            <tbody>
                                @foreach ($cart as $item)
                                    @php
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total += $subtotal;
                                    @endphp
                                    <tr>

                                        <td>{{ $item['name'] }}</td>
                                        <td class="text-center">{{ $item['quantity'] }}</td>
                                        <td class="text-right">&yen;{{ number_format($subtotal) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>



                            <!--
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-right">合計金額</th>
                                        <th class="text-right">&yen;{{ number_format($total) }}</th>
                                    </tr>
                                </tfoot>
                            -->

                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">小計</th>
                                    <th class="text-right">&yen;{{ $getCartItems['subtotal'] }}</th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-right">配送料</th>
                                    <th class="text-right">
                                        &yen;{{ $getCartItems['shipping_fee'] }}
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-right">合計金額</th>
                                    {{-- 小計と配送料を合算した最終合計金額を表示 --}}
                                    <th class="text-right">
                                        &yen;{{ $getCartItems['subtotal'] + $getCartItems['shipping_fee'] }}</th>
                                </tr>
                            </tfoot>


                        </table>
                    </div>
                </div>
            </div>

            <div class="button-area">

                <a href="{{ route('cart.index') }}" class="btn_return">戻る</a>

                <form action="{{ route('amazon-pay.create-session') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $getCartItems['subtotal'] + $getCartItems['shipping_fee'] }}">
                    <button type="submit" class="btn_payment">AmazonPayでお支払い</button>
                </form>

                <form action="{{ route('cart.square-payment') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn_payment">Squareでお支払い</button>
                </form>


            </div>
        </div>
    </main>
@endsection
