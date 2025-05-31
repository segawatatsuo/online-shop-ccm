@extends('layouts.app')


@section('head')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endsection


@section('content')
    <main class="main">

        <div class="container">

            @if (empty($cart))
                <p>カートは空です。</p>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'info',
                            title: 'カートが空です',
                            text: 'お買い物を続けてください',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    });
                </script>
            @else
                <h1>ショッピングカート</h1>

                <div class="tb-scrooll">
                    <table class="shop_table" cellspacing="0" style="width: 100%">
                        <thead>
                            <tr>

                                <th class="product-name">商品</th>
                                <th class="product-price">金額</th>
                                <th class="product-quantity">数量</th>
                                <th class="product-subtotal">小計</th>
                                <th class="product-remove"></th>
                            </tr>
                        </thead>
                        <tbody>



                            @php $total = 0; @endphp

                            @foreach ($cart as $item)
                                @php
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $total += $subtotal;
                                @endphp
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>¥{{ number_format($item['price']) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('cart.update') }}" class="d-flex">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                                min="1" class="form-control me-2" style="width:80px">
                                            <button class="btn btn-sm btn-primary">更新</button>
                                        </form>
                                    </td>
                                    <td>¥{{ number_format($subtotal) }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('cart.remove') }}" class="remove-form">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                            <button type="button" class="btn btn-sm btn-danger remove-btn">削除</button>
                                        </form>

                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>



                <div class="cart-collaterals">
                    <div class="cart_totals ">
                        <h2>お買い物カゴの合計</h2>

                        <table cellspacing="0">

                            <tr class="order-total">
                                <th>合計</th>
                                <td data-title="合計"><strong>
                                        <span class="woocommerce-Price-amount amount">
                                            <span
                                                class="woocommerce-Price-currencySymbol">&yen;</span>{{ number_format($total) }}</span></strong>
                                </td>
                            </tr>

                        </table>

                        <a href="{{ url()->previous() }}" class="btn btn-secondary">
                            <div class="a-button">買い物を続ける</div>
                        </a>

                        <a href="{{ route('order.create') }}">
                            <div class="a-button">購入手続きに進む</div>
                        </a>



                    </div>
                </div>
            @endif


        </div>
    </main>



@endsection
