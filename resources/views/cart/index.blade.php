@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/cart-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')
    <main class="container">
        <div class="cart-header">
            <h2>ショッピングカート</h2>
        </div>

        <div class="cart-table">
            <table>
                <thead>
                    <tr>
                        <th>商品名</th>
                        <th>金額</th>
                        <th>数量</th>
                        <th>小計</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp {{-- Initialize total outside the loop, before the tbody, or even before the table if you prefer. --}}
                    @foreach ($cart as $item)
                        @php
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td class="product-name">{{ $item['name'] }}</td>
                            <td class="product-price">&yen;{{ number_format($item['price']) }}</td>
                            <td>
                                <div class="quantity-controls">
                                    <form method="POST" action="{{ route('cart.update') }}" class="d-flex">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                        <input type="number" name="quantity" class="quantity-input"
                                            value="{{ $item['quantity'] }}" min="1">
                                        <button type="submit" class="update-btn" onclick="updateQuantity(this)">更新</button>
                                    </form>
                                </div>
                            </td>
                            <td class="subtotal">&yen;{{ number_format($subtotal) }}</td>
                            <td>
                                <form method="POST" action="{{ route('cart.remove') }}" class="remove-form">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                    <button type="submit" class="delete-btn remove-btn">削除</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach {{-- This closes the @foreach loop --}}
                </tbody>
            </table>
        </div>

        <div class="cart-total">
            <h4>お買い物カゴの合計</h4>
            {{-- Display the calculated total here --}}
            <div class="total-amount" id="totalAmount">&yen;{{ number_format($total) }}</div>
        </div>

        <div class="cart-actions">
            <button class="continue-shopping" onclick="window.location.href='{{ asset('product/'.$category) }}'">買い物を続ける</button>
             <button class="checkout-btn" onclick="window.location.href='{{ route('order.create') }}'">購入手続きに進む</button>
        </div>
    </main>

@endsection
