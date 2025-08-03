@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/kakunin-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')
<main class="main">
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">{{ __('パスワードをリセット') }}</h2>

            {{-- セッションメッセージ (成功時) --}}
            @if (session('status'))
                <div class="success-message" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">{{ __('メールアドレス') }}</label>
                    <input id="email" type="email" class="form-input @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="a-button" style="border: none; font-weight:400;">
                        {{ __('リセットリンクを送信') }}
                    </button>
                    <div>パスワードリセットリンクを送信します</div>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection