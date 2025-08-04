{{--}}
@extends('layouts.app')
<style>
.main {
    display: flex;
    flex-direction: column;  /* ← これを追加 */
    justify-content: center;
    align-items: center;
    min-height: 300px;
    text-align: center;
}
</style>
@section('content')
    <main class="main">
        <h1>特定商取引法に基づく表示</h1>
        <img src="{{ asset('images/junbi_icon.png') }}" alt="">
    </main>
@endsection
--}}

@extends('layouts.app')

@section('title', '特定商取引法に基づく表示')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/cart-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush


@section('content')
    <div class="container py-4">
        <h1 class="mb-4">特定商取引法に基づく表示</h1>
        <div>
            {!! nl2br(e($legalContent)) !!}
        </div>
    </div>
@endsection