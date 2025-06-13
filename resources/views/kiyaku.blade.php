@extends('layouts.app')
<style>
.main {
    display: flex;
    flex-direction: column;  /* ← これを追加 */
    justify-content: center;
    align-items: center;
    min-height: 300px;
    text-align: center;
}
</style>
@section('content')
    <main class="main">
        <h1>利用規約</h1>
        <img src="{{ asset('images/junbi_icon.png') }}" alt="">
    </main>
@endsection
