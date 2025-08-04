@extends('layouts.app') {{-- 必要に応じて調整 --}}
@section('title', 'お問い合わせ')
@section('head')
<style>
main.main {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-height: 80vh;
    padding: 2rem;
    background-color: #f9f9f9; /* 背景色（お好みで） */
}

main.main form {
    width: 100%;
    max-width: 500px;
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
}

main.main form div {
    margin-bottom: 1.5rem;
    display: flex;
    flex-direction: column;
}

main.main form label {
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #333;
}

main.main form input,
main.main form textarea {
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

main.main form input:focus,
main.main form textarea:focus {
    outline: none;
    border-color: #007bff;
}

main.main form button {
    background-color: #007bff;
    color: white;
    padding: 0.75rem;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s;
}

main.main form button:hover {
    background-color: #0056b3;
}

</style>
@endsection

@section('content')

    <main class="main">
        <h1>お問い合わせ</h1>

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contact.submit') }}">
            @csrf
            <div>
                <label>お名前:</label>
                <input type="text" name="name" value="{{ old('name') }}">
            </div>
            <div>
                <label>メールアドレス:</label>
                <input type="email" name="email" value="{{ old('email') }}">
            </div>
            <div>
                <label>お問い合わせ内容:</label>
                <textarea name="message">{{ old('message') }}</textarea>
            </div>
            <div>
                <button type="submit">送信</button>
            </div>
        </form>
    </main>

@endsection
