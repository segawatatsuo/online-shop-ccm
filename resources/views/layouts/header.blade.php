<header class="header">
    <div class="header-inner">
        <a class="header-logo" href="{{ url('/') }}">
            <img src="{{ asset('images/top/logo.png') }}" alt="">
        </a>
        <button class="toggle-menu-button"></button>
        <div class="header-site-menu">
            <nav class="site-menu">
                <ul>
                    <li><a href="https://www.ccmedico.com/">CCM</a></li>
                    <li><a href="{{ asset('product/airstocking') }}">エアストッキング&reg;</a></li>
                    <li><a href="{{ asset('product/gelnail') }}">3in1&reg;ジェルネイル</a></li>
                    <li><a href="{{ asset('product/wax') }}">美脚脱毛</a></li>

                    {{-- カートが空でなければ表示 --}}
                    @if(session('cart') && count(session('cart')) > 0)
                        <li><a href="{{ url('/cart') }}">カート ({{ count(session('cart')) }})</a></li>
                    @endif



                </ul>
            </nav>
        </div>
    </div>
</header>
