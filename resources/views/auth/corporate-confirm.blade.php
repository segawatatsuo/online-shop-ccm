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

        @media (max-width: 768px) {
            .a-button {
                width: 100%;
                max-width: 350px;
                padding: 15px 100px;
                font-size: 1rem;
                margin-bottom: 15px;
            }
        }
    </style>
@endpush

@section('content')

    <main class="main">
        <div class="container">
            <h2 class="section-title">法人会員登録内容</h2>



            <div class="info-card">
                <div class="card-header">
                    <h3>会社情報</h3>
                </div>
                <div class="card-body grid-layout">
                    @php $address = session('corporate_register_data'); @endphp
                    <div><strong>氏名:</strong> {{ $address['order_sei'] }} {{ $address['order_mei'] }}</div>
                    <div><strong>メール:</strong> {{ $address['email'] }}</div>
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
                    <div><strong>会社名：</strong> {{ $address['delivery_company_name'] }}</div>
                    <div><strong>部署名：</strong> {{ $address['delivery_department'] }}</div>
                    <div><strong>担当者：</strong> {{ $address['delivery_sei'] }} {{ $address['delivery_mei'] }}</div>
                    <div><strong>電話番号：</strong> {{ $address['delivery_phone'] }}</div>
                    <div><strong>郵便番号：</strong> {{ $address['delivery_zip'] }}</div>
                    <div class="grid-full"><strong>住所：</strong> {{ $address['delivery_add01'] }}
                        {{ $address['delivery_add02'] }} {{ $address['delivery_add03'] }}</div>
                </div>
            </div>



            {{-- ボタン 
            <div class="text-center mt-4">
                <a href="{{ route('corporate.register') }}" class="btn btn-secondary">戻る</a>
                <form action="{{ route('corporate.register.store') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="a-button" style="border: none">登録</button>
                </form>
            </div>
            --}}

            <div class="button-area">

                <a href="{{ route('corporate.register') }}" class="btn btn-secondary">戻る</a>

                <form action="{{ route('corporate.register.store') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="a-button" style="border: none">登 録</button>
                </form>

            </div>



        </div>
    </main>
@endsection
