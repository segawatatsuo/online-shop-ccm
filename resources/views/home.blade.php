@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
@endsection


@section('content')
<main class="main-page">
    <div class="central-container">
        <div class="card">
            <div class="card-header">法人会員ページ</div>

            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @foreach ($categories as $category)
                    <div class="brand-card">
                        <a href="{{ asset('product/'.$category->brand) }}">
                        {{ $category->brand_name }}
                        </a>
                    </div>
                @endforeach

                <!-- ログアウトボタン（下部に移動） -->
                <form action="{{ route('logout') }}" method="POST" class="logout-form-bottom">
                    @csrf
                    <button type="submit" class="logout-button">ログアウト</button>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
