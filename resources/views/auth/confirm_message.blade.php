@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        /* すっきりした alert 用カスタムクラス */
        .custom-alert {
            background-color: #e9f7ef;
            /* 薄いグリーン */
            color: #2e7d32;
            /* 文字は落ち着いた緑 */
            padding: 12px 16px;
            border-radius: 8px;
            border: 1px solid #c8e6c9;
            font-size: 0.95rem;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 10px
        }

        .btn.btn-primary {
            background-color: #1976d2;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-size: 0.95rem;
        }

        .btn.btn-primary:hover {
            background-color: #1565c0;
        }
    </style>
@endpush

@section('content')
    <main class="main">
        <div class="login-container">
            <div class="login-card">
                <h2 class="login-title">{{ __('確認メールを送信しました') }}</h2>

                <p class="login-message">
                    法人会員登録ありがとうございます。<br>
                    ご登録いただきましたメールアドレスに確認メールを送信しました。メールを確認し、認証を完了してください。<br><br>
                    CCメディコ
                </p>

                @if (session('status'))
                    <div class="alert alert-success mt-3">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="custom-alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('corporate.verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary mt-3">認証メールを再送する</button>
                </form>

            </div>
        </div>
    </main>
@endsection
