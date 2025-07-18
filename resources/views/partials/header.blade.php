<header class="header">
    <nav class="nav-container">
        <div class="logo">
            <a href="{{ asset('/') }}">
                <img src="{{ asset('storage/images/processed/logo.png') }}" alt="ccmedico">
            </a>
        </div>
        <ul class="nav-menu" id="navMenu">
            <li><a href="https://www.ccmedico.com/">CCM</a></li>
            <li><a href="{{ asset('product/airstocking') }}">エアストッキング&reg;</a></li>
            <li><a href="{{ asset('product/gelnail') }}">3in1&reg;ジェルネイル</a></li>
            <li><a href="{{ asset('product/wax') }}">美脚脱毛</a></li>

            <!-- ログアウトボタン -->
            @if (Auth::check() && !Route::is('home'))
                <form action="{{ route('logout') }}" method="POST" class="logout-form-bottom">
                    @csrf
                    <button type="submit" class="logout-button">ログアウト</button>
                </form>
            @endif
            {{-- カートが空でなければ表示 --}}
            @if (session('cart') && count(session('cart')) > 0)
                <li><a href="{{ url('/cart') }}"><span title="カート">🛒</span>（ {{ count(session('cart')) }}
                        ）</a></li>
            @endif

        </ul>
        <div class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </nav>
</header>
