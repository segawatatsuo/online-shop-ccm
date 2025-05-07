<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>エアーストッキング</title>
    <script src="{{ asset('js/toggle-menu.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/concept.css') }}">
    <link rel="stylesheet" href="{{ asset('css/menu.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/button.css') }}">
    <link rel="stylesheet" href="{{ asset('css/top.css') }}">
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <script src="https://kit.fontawesome.com/f57af4dcea.js" crossorigin="anonymous"></script>
</head>

<body>

    <header class="header">
        <div class="header-inner">
            <a class="header-logo" href="index.html"><img src="{{ asset('images/top/logo.png') }}" alt=""> </a>

            

            <button class="toggle-menu-button"></button>
            <div class="header-site-menu">
                <nav class="site-menu">
                    <ul>
                        <li><a href="https://www.ccmedico.com/">CCM</a></li>
                        <li><a href="{{ asset('product/airstocking') }}">エアストッキング&reg;</a></li>
                        <li><a href="{{ asset('product/gelnail') }}">3in1&reg;ジェルネイル</a></li>
                        <li><a href="{{ asset('product/wax') }}">美脚脱毛</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>





    <main class="main">
        <div class="line-up">
            <a href="{{ asset('product/wax') }}"><img src="{{ asset('images/shop-top/bikyaku.jpg') }}" alt="" class="pic"></a>
            <a href="{{ asset('product/airstocking') }}"><img src="{{ asset('images/shop-top/daimond.jpg') }}" alt="" class="pic"></a>
            <a href="{{ asset('product/gelnail') }}"><img src="{{ asset('images/shop-top/3in1Lineup1560-600.jpg') }}" alt="" class="pic"></a>
        </div>


    </main>

    <footer>
        <div class="footer">
            <div class="row">
                <a href="#"><i class="fa fa-facebook"></i></a>
                <a href="#"><i class="fa fa-instagram"></i></a>
                <a href="#"><i class="fa fa-youtube"></i></a>
                <a href="#"><i class="fa fa-twitter"></i></a>
            </div>

            <div class="row">
                <ul>
                    <li><a href="inquiry.html">お問い合わせ</a></li>
                    <li><a href="kiyaku.html">利用規約</a></li>
                    <li><a href="privacy.html">個人情報保護について</a></li>
                    <li><a href="houjin.html">CCM法人取引専用サイト</a></li>
                    <li><a href="tokutei.html">特定商取引法に基づく表示</a></li>
                </ul>
            </div>




            <div class="row">
                〒150-0043 東京都渋谷区道玄坂1-12-1
                渋谷マークシティ W22階
                TEL：03-6897-4086／FAX：03-6735-4829
            </div>
            <div class="row">
                Copyright C.C.Medico Co.,Ltd　All Rights Reserved.
            </div>
        </div>
    </footer>
</body>

</html>