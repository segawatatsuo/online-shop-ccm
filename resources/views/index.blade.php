@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}

    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush



@section('content')


    <main class="main">
        <div class="line-up">
            <a href="{{ asset('product/wax') }}"><img src="{{ asset('images/shop-top/bikyaku.jpg') }}" alt="" class="pic"></a>
            <a href="{{ asset('product/airstocking') }}"><img src="{{ asset('images/shop-top/daimond.jpg') }}" alt="" class="pic"></a>
            <a href="{{ asset('product/gelnail') }}"><img src="{{ asset('images/shop-top/3in1Lineup1560-600.jpg') }}" alt="" class="pic"></a>
        </div>


    </main>

@endsection