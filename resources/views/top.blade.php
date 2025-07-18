@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/top-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')

    @if ($category === 'airstocking')

        @if (!Auth::check() || Auth::user()->user_type !== 'corporate')
            <!-- ヒーローセクション -->
            <section class="hero">
                <div class="hero-content">
                    <!--
                                        <h1>Modern Design</h1>
                                        <p>シンプルでリッチなデザインの世界へようこそ</p>
                                        -->
                </div>
            </section>

            <!-- セクション1 -->
            <section class="section1">
                <div class="container">
                    <div class="feature-row fade-in">
                        <div class="feature-content">
                            <h3>履かないストッキング</h3>
                            <p>「エアーストッキング」は〜（中略）〜すぐに塗ることができます。</p>
                        </div>
                        <div class="feature-image">
                            <img src="{{ asset('storage/images/processed/main1_800x_q80.jpg') }}" alt="Feature 1">
                        </div>
                    </div>

                    <div class="feature-row fade-in">
                        <div class="feature-content">
                            <h3>Feature Two</h3>
                            <p>二番目の特徴的な機能について詳しくご説明いたします。革新的な技術と美しいデザインが融合した製品です。</p>
                        </div>
                        <div class="feature-image">
                            <img src="{{ asset('storage/images/processed/types_800x_q80.jpg') }}" alt="Feature 1">
                        </div>
                    </div>

                    <div class="feature-row fade-in">
                        <div class="feature-content">
                            <h3>Feature Three</h3>
                            <p>三番目の機能は特に注目していただきたいポイントです。お客様のニーズに合わせてカスタマイズ可能な仕様となっております。</p>
                        </div>
                        <div class="feature-image">
                            <img src="{{ asset('storage/images/processed/color_800x_q80.jpg') }}" alt="Feature 1">
                        </div>
                    </div>
                </div>
            </section>

            <!-- セクション2 - 動画 -->
            <section class="section2">
                <div class="container">
                    <div class="video-container fade-in">
                        <h2>MOVIE</h2>
                        <div class="video-wrapper">
                            <!--<iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" allowfullscreen></iframe>-->
                            <iframe
                                src="https://m.media-amazon.com/images/S/al-jp-eb5039ce-f881/7b6da0ed-910c-4b6d-ade2-37d055e15396.mp4"
                                allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        
        <!-- セクション3 - 商品 -->
        <section class="section3">
            <div class="container">
                <h2 class="fade-in">LINE UP</h2>

                <div class="product-block fade-in">
                    <h3>PREMIUM SILK</h3>
                    <div class="product-grid">
                        @foreach ($premiumSilk as $product)
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="{{ asset('product/airstocking/' . $product->id) }}">
                                        @if ($product->mainImage)
                                            <img src="{{ url('uploads/' . $product->mainImage->image_path) }}"
                                                alt="">
                                        @else
                                            <img src="{{ asset('images/noimage.png') }}" alt="画像なし">
                                        @endif
                                    </a>
                                </div>
                                <div class="product-code">PS-001</div>
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-price">￥{{ number_format($product->price) }}</div>
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="number" class="quantity-input" value="1" min="1">
                                    <button class="add-to-cart ">カートに入れる</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="product-block fade-in">
                    <h3>DIAMOND LEGS</h3>
                    <div class="product-grid">
                        @foreach ($diamondLegs as $product)
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="{{ asset('product/airstocking/' . $product->id) }}">
                                        @if ($product->mainImage)
                                            <img src="{{ url('uploads/' . $product->mainImage->image_path) }}"
                                                alt="">
                                        @else
                                            <img src="{{ asset('images/noimage.png') }}" alt="画像なし">
                                        @endif
                                    </a>
                                </div>
                                <div class="product-code">PS-001</div>
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-price">￥{{ number_format($product->price) }}</div>
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="number" class="quantity-input" value="1" min="1">
                                    <button class="add-to-cart ">カートに入れる</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @else
        <main class="main"
            style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 80vh; text-align: center;">
            <h2>{{ ucfirst($category) }} 商品一覧</h2>
            {{-- gelnail や wax など他のカテゴリ --}}
            <p>このカテゴリには現在登録されている商品がありません。</p>
        </main>
    @endif

@endsection
