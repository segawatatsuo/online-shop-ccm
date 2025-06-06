@extends('layouts.app')

@section('head')
<style>
    main.main {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
        padding: 2rem;
        background-color: #f9f9f9;
        box-sizing: border-box;
    }

    main.main h2 {
        font-size: 1.8rem;
        margin-bottom: 1rem;
        color: #333;
        text-align: center;
    }

    main.main p {
        font-size: 1.1rem;
        color: #555;
        text-align: center;
        margin: 0;
    }

    main.main .thanks-card {
        background: #fff;
        padding: 2.5rem 2rem;
        border-radius: 12px;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.05);
        text-align: center;
        max-width: 500px;
        width: 100%;
    }
</style>
@endsection

@section('content')
    <main class="main">
        <div class="thanks-card">
            <h2>お問い合わせありがとうございました</h2>
            <p>送信が完了しました。後ほど担当者よりご連絡いたします。少々お待ちください。</p>
        </div>
    </main>
@endsection
