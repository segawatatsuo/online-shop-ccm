@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@section('content')
<main class="main">
    <div class="login-container">
        <div class="login-card">
            <h2 class="login-title">{{ __('ログイン') }}</h2>
            <form method="POST" action="{{ route('login') }}">
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

                <div class="form-group">
                    <label for="password" class="form-label">{{ __('パスワード') }}</label>
                    <input id="password" type="password" class="form-input @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group-checkbox">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('ログイン情報を記憶する') }}
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="a-button" style="border: none">
                        {{ __('ログイン') }}
                    </button>

                    @if (Route::has('password.request'))
                        <a class="btn-link" href="{{ route('password.request') }}">
                            {{ __('パスワードを忘れた場合') }}
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <a class="btn-link" href="{{ route('corporate.register') }}">
                            {{ __('新規登録') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
