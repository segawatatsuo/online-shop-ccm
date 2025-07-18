@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/complete.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')

    <main class="main">
        <div style="display: block;">
            <h1>ご注文ありがとうございます</h1>

            <p>
                お客様のご注文を受け付けいたしました<br>
                商品の到着までどうぞ楽しみにお待ちください。<br>
                何かご不明な点やご要望がございましたら、お気軽にお問い合わせください。<br>
                今後ともCCメディコをどうぞよろしくお願いいたします。
            </p>
            <br>
            <p><a href="{{ route('products.index') }}">CCメディコトップページ</a></p>
        </div>
    </main>
@endsection
