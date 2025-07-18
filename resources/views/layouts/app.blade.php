<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Modern Responsive Site')</title>

    {{-- @head --}}
    @yield('head')

    {{-- 共通CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    {{-- <link rel="stylesheet" href="{{ asset('css/style.css') }}">--}}
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    {{-- ページごとの追加CSS --}}
    @stack('styles')
</head>

<body>

    {{-- ヘッダー --}}
    @include('partials.header')

    {{-- コンテンツ --}}
    @yield('content')

    {{-- フッター --}}
    @include('partials.footer')

    {{-- スクリプト --}}
    <script src="{{ asset('js/script.js') }}"></script>

    <!-- bladeに書かれたJavaScriptの読み込み位置（通常はbodyの最後に） -->
    @stack('scripts')
</body>

</html>
