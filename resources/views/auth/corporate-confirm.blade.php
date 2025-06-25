@extends('layouts.app') 

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/address_input.css') }}">
    <link rel="stylesheet" href="{{ asset('css/confirm.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endpush

@section('content')

<main class="main">
<div class="container">
    <h2 class="mb-4">法人会員登録内容</h2>

    {{-- ご注文者情報 --}}
    <div class="card mb-4">
        <div class="card-header">会社情報</div>
        <div class="card-body grid-2">
            @php $address = session('corporate_register_data'); @endphp
            <div><strong>会社名：</strong> {{ $address['order_company_name'] }}</div>
            <div><strong>部署名：</strong> {{ $address['order_department'] }}</div>
            <div><strong>担当者：</strong> {{ $address['order_sei'] }} {{ $address['order_mei'] }}</div>
            <div><strong>メール：</strong> {{ $address['email'] }}</div>
            <div><strong>電話番号：</strong> {{ $address['order_phone'] }}</div>
            <div><strong>郵便番号：</strong> {{ $address['order_zip'] }}</div>
            <div class="grid-full"><strong>住所：</strong> {{ $address['order_add01'] }} {{ $address['order_add02'] }} {{ $address['order_add03'] }}</div>
            <div><strong>ホームページ：</strong> {{ $address['homepage'] }}</div>
        </div>
    </div>

    {{-- お届け先情報 --}}
    <div class="card mb-4">
        <div class="card-header">お届け先情報</div>
        <div class="card-body grid-2">
            <div><strong>会社名：</strong> {{ $address['delivery_company_name'] }}</div>
            <div><strong>部署名：</strong> {{ $address['delivery_department'] }}</div>
            <div><strong>担当者：</strong> {{ $address['delivery_sei'] }} {{ $address['delivery_mei'] }}</div>
            <div><strong>電話番号：</strong> {{ $address['delivery_phone'] }}</div>
            <div><strong>郵便番号：</strong> {{ $address['delivery_zip'] }}</div>
            <div class="grid-full"><strong>住所：</strong> {{ $address['delivery_add01'] }} {{ $address['delivery_add02'] }} {{ $address['delivery_add03'] }}</div>
        </div>
    </div>



    {{-- ボタン --}}
    <div class="text-center mt-4">
        <a href="{{ route('corporate.register') }}" class="btn btn-secondary">戻る</a>

        <form action="{{ route('corporate.register.store') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="a-button" style="border: none">登録</button>
        </form>
    </div>


</div>
</main>
@endsection
