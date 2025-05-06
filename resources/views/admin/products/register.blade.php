@extends('layouts.app')

@section('content')
<div class="container">
    <h2>管理者登録</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.register') }}">
        @csrf

        <div class="mb-3">
            <label>名前</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>メールアドレス</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>パスワード</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>パスワード確認</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">登録</button>
    </form>
</div>
@endsection
