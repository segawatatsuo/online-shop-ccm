@extends('layouts.app')

@section('title', 'トップページに戻る')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/complete.css') }}">
    <link rel="stylesheet" href="{{ asset('css/amazonpay_complete.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')




@endsection