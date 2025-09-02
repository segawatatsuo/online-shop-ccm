@extends('layouts.app')

@section('title', '決済が完了しました')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/complete.css') }}">
    <link rel="stylesheet" href="{{ asset('css/amazon_complate.css') }}">

    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')


    <main class="main">
        <div class="success-container">
            <div class="success-icon">✓</div>
            <div class="success-message">決済が完了しました</div>

            <div class="order-info">
                <h3>ご注文詳細</h3>
                <p><strong>決済金額:</strong> ¥{{ $amount ?? '' }}</p>
                <p><strong>メールアドレス:</strong> {{ $email ?? '' }}</p>
                @if (isset($orderData['checkoutSessionId']))
                    <p><strong>注文ID:</strong> {{ $orderData['checkoutSessionId'] }}</p>
                @endif
                <p><strong>決済日時:</strong> {{ now()->format('Y年m月d日 H:i:s') }}</p>
            </div>

            <p>ご注文ありがとうございました。<br>
                確認メールを送信いたします。</p>

            <a href="{{ url('/') }}" class="back-button">トップページに戻る</a>
        </div>
    </main>
@endsection
