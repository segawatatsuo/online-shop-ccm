@extends('layouts.app')

@section('content')
    <h1>ご注文ありがとうございました！</h1>
    <p>注文番号：#{{ $order->id }}</p>
    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">トップに戻る</a>
@endsection
