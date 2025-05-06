@extends('layouts.app')

@section('content')
    <h1>注文確認</h1>

    <form method="POST" action="{{ route('order.complete') }}">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        

        <div class="mb-3">
            <label>お名前</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', Auth::user()->name ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label>メールアドレス</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', Auth::user()->email ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label>電話番号</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', Auth::user()->phone ?? '') }}">
        </div>
        <div class="mb-3">
            <label>住所</label>
            <input type="text" name="address" class="form-control"  value="{{ old('address', Auth::user()->address ?? '') }}">
        </div>

        <h4>注文内容</h4>
        <ul>
            @foreach ($cart as $item)
                <li>{{ $item['name'] }} x {{ $item['quantity'] }}：¥{{ number_format($item['price'] * $item['quantity']) }}
                </li>
            @endforeach
        </ul>

        <button type="submit" class="btn btn-primary">注文を確定</button>
    </form>
@endsection
