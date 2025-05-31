<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>エアーストッキング</title>
    <script src="{{ asset('js/toggle-menu.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/concept.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/button.css') }}">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    {{-- 各ページ専用のCSSなどをここで差し込める --}}
    @yield('head')


    <script src="https://kit.fontawesome.com/f57af4dcea.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- ▼ここにbladeに書かれたCSSを適宜追加！ --}}
        @stack('styles')

</head>

<body>

    {{-- 共通ヘッダー --}}
    @include('layouts.header')

    <!--<main class="main">-->
        @yield('content')
    <!--</main>-->

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 削除ボタンのイベント
                document.querySelectorAll('.remove-btn').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const form = this.closest('form');

                        Swal.fire({
                            title: '削除してもよろしいですか？',
                            text: "この商品をカートから削除します。",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'はい、削除します',
                            cancelButtonText: 'キャンセル',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                // フラッシュメッセージ SweetAlert2 表示(商品をカートに追加しました！は非表示)
                @if (session('success') && session('success') !== '商品をカートに追加しました！')
                    Swal.fire({
                        icon: 'success',
                        title: '完了！',
                        text: '{{ session('success') }}',
                        showConfirmButton: false,
                        timer: 1800
                    });
                @endif
            });
        </script>
    @endpush




    @stack('scripts') {{-- ← ここでbladeに書かれたJavascriptJavascriptを適宜追加！ --}}
    {{-- 共通フッター --}}
    @include('layouts.footer')

</body>

</html>
