@extends('layouts.app')

@section('content')
    <h1>商品登録</h1>
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.products.form')
        <button type="submit">登録</button>
    </form>
@endsection
