@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            認証メールを再送しました。
        </div>
    @endif

    <p>登録されたメールアドレス宛に確認リンクを送信しました。</p>
    <p>メールをご確認のうえ、リンクをクリックして認証を完了してください。</p>

    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn btn-link">確認メールを再送信する</button>
    </form>
</div>
@endsection
