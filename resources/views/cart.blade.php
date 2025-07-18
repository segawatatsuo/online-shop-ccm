@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/cart-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')
    <!-- メインコンテンツ -->
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
                    <tr>
                        <td class="product-name">プレミアムシルク A</td>
                        <td class="product-price">\3,980</td>
                        <td>
                            <div class="quantity-controls">
                                <input type="number" class="quantity-input" value="1" min="1" data-price="3980">
                                <button class="update-btn" onclick="updateQuantity(this)">更新</button>
                            </div>
                        </td>
                        <td class="subtotal">\3,980</td>
                        <td>
                            <button class="delete-btn" onclick="deleteItem(this)">削除</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="product-name">ダイヤモンドレッグス B</td>
                        <td class="product-price">\6,280</td>
                        <td>
                            <div class="quantity-controls">
                                <input type="number" class="quantity-input" value="2" min="1" data-price="6280">
                                <button class="update-btn" onclick="updateQuantity(this)">更新</button>
                            </div>
                        </td>
                        <td class="subtotal">\12,560</td>
                        <td>
                            <button class="delete-btn" onclick="deleteItem(this)">削除</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="product-name">プレミアムシルク C</td>
                        <td class="product-price">\3,780</td>
                        <td>
                            <div class="quantity-controls">
                                <input type="number" class="quantity-input" value="1" min="1" data-price="3780">
                                <button class="update-btn" onclick="updateQuantity(this)">更新</button>
                            </div>
                        </td>
                        <td class="subtotal">\3,780</td>
                        <td>
                            <button class="delete-btn" onclick="deleteItem(this)">削除</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="cart-total">
            <h4>お買い物カゴの合計</h4>
            <div class="total-amount" id="totalAmount">\20,320</div>
        </div>

        <div class="cart-actions">
            <button class="continue-shopping" onclick="continueShopping()">買い物を続ける</button>
            <button class="checkout-btn" onclick="proceedToCheckout()">購入手続きに進む</button>
        </div>
    </main>

@endsection
