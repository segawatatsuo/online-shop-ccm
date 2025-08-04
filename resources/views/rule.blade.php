{{--
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
        <h1>利用規約</h1>
        <img src="{{ asset('images/junbi_icon.png') }}" alt="">
    </main>
@endsection
--}}

@extends('layouts.app')

@section('title', '利用規約')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/cart-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush


@section('content')
    <div class="container py-4">
        <h1 class="mb-4">利用規約</h1>
        <div>
            {!! nl2br(e($ruleContent)) !!}
        </div>
    </div>
@endsection