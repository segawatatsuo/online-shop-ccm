<form method="POST" action="{{ route('register.store') }}">
    @csrf
    <p>名前：{{ $input['name'] }}</p>
    <p>メール：{{ $input['email'] }}</p>
    <button type="submit">登録する</button>
</form>

<form method="GET" action="{{ route('register') }}">
    <button type="submit">戻る</button>
</form>
