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
            <section class="hero"
                style="--hero-bg-image: url('{{ asset('uploads/' . optional($topPageItem)->hero_img) }}');">
                <div class="hero-content">

                    <h1 style="color: {{ optional($topPageItem)->hero_head_copy_color }}">
                        {{ optional($topPageItem)->hero_head_copy }}</h1>
                    <p style="color: {{ optional($topPageItem)->hero_lead_copy_color }}">
                        {{ optional($topPageItem)->hero_lead_copy }}</p>

                </div>
            </section>

            <!-- セクション -->
            <section class="section1">
                <div class="container">

                    <!-- section1 -->
                    @if (optional($topPageItem)->section1_display_hide == '1')
                        <div class="feature-row fade-in">
                            <div class="feature-content">
                                <h3>{{ optional($topPageItem)->section1_head_copy }}</h3>
                                <p>{{ optional($topPageItem)->section1_copy }}</p>
                            </div>
                            <div class="feature-image">
                                <img src="{{ asset('uploads/' . optional($topPageItem)->section1_img) }}">
                            </div>
                        </div>
                    @endif
                    <!-- section2 -->
                    @if (optional($topPageItem)->section2_display_hide == '1')
                        <div class="feature-row fade-in">
                            <div class="feature-content">
                                <h3>{{ optional($topPageItem)->section2_head_copy }}</h3>
                                <p>{{ optional($topPageItem)->section2_copy }}</p>
                            </div>
                            <div class="feature-image">
                                <img src="{{ asset('uploads/' . optional($topPageItem)->section2_img) }}">
                            </div>
                        </div>
                    @endif
                    <!-- section3 -->
                    @if (optional($topPageItem)->section3_display_hide == '1')
                        <div class="feature-row fade-in">
                            <div class="feature-content">
                                <h3>{{ optional($topPageItem)->section3_head_copy }}</h3>
                                <p>{{ optional($topPageItem)->section3_copy }}</p>
                            </div>
                            <div class="feature-image">
                                <img src="{{ asset('uploads/' . optional($topPageItem)->section3_img) }}">
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            <!-- セクション2 - 動画 -->
            @if (optional($topPageItem)->movie_section_display_hide == '1')
                <section class="section2">
                    <div class="container">
                        <div class="video-container fade-in">
                            <h2>MOVIE</h2>
                            <div class="video-wrapper">
                                <iframe
                                    src="{{ optional($topPageItem)->movie_section_url }}"
                                    allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

        @endif


        <!-- セクション3 - 商品 -->
        <section class="section3">
            <div class="container">
                <h2 class="fade-in">LINE UP</h2>

                <div class="product-block fade-in">
                    <h3>DIAMOND LEGS</h3>
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
                                <div class="product-code">{{ $product->product_code }}</div>
                                <div class="product-name">{{ $product->name }}</div>

                                <!-- 価格とフォーム部分を下揃えにするためのラッパー -->
                                <div class="product-bottom">
                                    <div class="product-price">￥{{ number_format($product->price) }}</div>
                                    <form method="POST" action="{{ route('cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="number" class="quantity-input" value="1" min="1" name="quantity" >
                                        <button class="add-to-cart">カートに入れる</button>
                                    </form>
                                </div>
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
                                <div class="product-code">{{ $product->product_code }}</div>
                                <div class="product-name">{{ $product->name }}</div>

                                <!-- 価格とフォーム部分を下揃えにするためのラッパー -->
                                <div class="product-bottom">
                                    <div class="product-price">￥{{ number_format($product->price) }}</div>
                                    <form method="POST" action="{{ route('cart.add') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="number" class="quantity-input" value="1" min="1" name="quantity" >
                                        <button class="add-to-cart">カートに入れる</button>
                                    </form>
                                </div>
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
