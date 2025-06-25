@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('css/corporate_confirm.css') }}">
@endpush

@section('content')
    <main class="main">
        <div class="order-container">
            <h1 class="order-title">ご注文情報確認</h1>

            <div class="order-summary-wrapper">
                <div class="order-card order-shipping-address">
                    <h2 class="order-card-title">お届け先</h2>

                    <div class="order-field">
                        <span class="order-label">郵便番号:</span>
                        <span class="order-value">{{ $user->corporateCustomer->delivery_zip ?? '' }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">会社名:</span>
                        <span class="order-value">{{ $user->corporateCustomer->delivery_company_name ?? '' }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">部署名:</span>
                        <span class="order-value">{{ $user->corporateCustomer->delivery_department ?? '' }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">お名前:</span>
                        <span
                            class="order-value">{{ ($user->corporateCustomer->delivery_sei ?? '') . ' ' . ($user->corporateCustomer->delivery_mei ?? '') }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">住所:</span>
                        <span
                            class="order-value">{{ ($user->corporateCustomer->delivery_add01 ?? '') . ' ' . ($user->corporateCustomer->delivery_add02 ?? '') . ' ' . ($user->corporateCustomer->delivery_add03 ?? '') }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">電話番号:</span>
                        <span class="order-value">{{ $user->corporateCustomer->delivery_phone ?? '' }}</span>
                    </div>

                    <div style="text-align: right">
                        <span class=""><a href="{{ route('order.modify', ['type' => 'delivery']) }}">変更</a></span>
                    </div>
                </div>

                <div class="order-card order-billing-address">
                    <h2 class="order-card-title">ご注文者</h2>
                    <div class="order-field">
                        <span class="order-label">郵便番号:</span>
                        <span class="order-value">{{ $user->corporateCustomer->order_zip ?? '' }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">会社名:</span>
                        <span class="order-value">{{ $user->corporateCustomer->order_company_name ?? '' }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">部署名:</span>
                        <span class="order-value">{{ $user->corporateCustomer->order_department ?? '' }}</span>
                    </div>

                    <div class="order-field">
                        <span class="order-label">お名前:</span>
                        <span
                            class="order-value">{{ ($user->corporateCustomer->order_sei ?? '') . ' ' . ($user->corporateCustomer->order_mei ?? '') }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">住所:</span>
                        <span
                            class="order-value">{{ ($user->corporateCustomer->order_add01 ?? '') . ' ' . ($user->corporateCustomer->order_add02 ?? '') . ' ' . ($user->corporateCustomer->order_add03 ?? '') }}</span>
                    </div>
                    <div class="order-field">
                        <span class="order-label">電話番号:</span>
                        <span class="order-value">{{ $user->corporateCustomer->order_phone ?? '' }}</span>
                    </div>
                    <div style="text-align: right">
                        <span class=""><a href="{{ route('order.modify', ['type' => 'order']) }}">変更</a></span>
                    </div>

                </div>
            </div>

            <!-- ここにお届け希望日時のカードを追加 -->
            <div class="order-card order-delivery-info-card">
                <h2 class="order-card-title">お届け希望日時</h2>
                <div class="order-field">
                    <span class="order-label">お届け希望日:</span>
                    <span class="order-value"><input type="date" id="delivery_date" name="delivery_date"></span>
                </div>
                <div class="order-field">
                    <span class="order-label">お届け希望時間:</span>
                    <span class="order-value">
                        <select class="form-select" id="delivery_time" name="delivery_time">
                            @foreach ($deliveryTimes as $time)
                                <option value="{{ $time }}" {{ old('delivery_time') == $time ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                            @endforeach
                        </select>
                        @error('delivery_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </span>




                </div>
            </div>

            <div class="order-card order-items-card">
                <h2 class="order-card-title">ご注文商品</h2>
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th class="order-table-header">商品番号</th>
                            <th class="order-table-header">商品名</th>
                            <th class="order-table-header">数量</th>
                            <th class="order-table-header">単価</th>
                            <th class="order-table-header">小計</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Bladeのループ処理 -->
                        @if (isset($cart) && count($cart) > 0)
                            @foreach ($cart as $item)
                                <tr>
                                    <td class="order-table-data" data-label="商品番号">{{ $item['product_code'] }}</td>
                                    <td class="order-table-data" data-label="商品名">{{ $item['name'] }}</td>
                                    <td class="order-table-data" data-label="数量">{{ $item['quantity'] }}</td>
                                    <td class="order-table-data" data-label="単価">{{ number_format($item['price']) }}</td>
                                    <td class="order-table-data" data-label="小計">
                                        {{ number_format($item['price'] * $item['quantity']) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="order-table-data" style="text-align: center;">カートに商品がありません。</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="order-total-card">
                <h2 class="order-total-title">合計金額</h2>
                <div class="order-total-amount">￥{{ number_format($total ?? 0) }}</div>
            </div>

        </div>
    </main>

@endsection
