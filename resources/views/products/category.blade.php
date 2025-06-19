@extends('layouts.app')


@section('head')
    <link rel="stylesheet" href="{{ asset('css/imagefit.css') }}">
@endsection


@section('content')

@if ($category === 'airstocking')
    <main class="main">

        {{-- 一般会員（法人でない人）のみ：上部の説明を表示 --}}
        @if (!Auth::check() || Auth::user()->user_type !== 'corporate')
            <div class="first-view">
                <div class="first-view-text">
                    <h1></h1>
                    <p></p>
                </div>
            </div>

            <div class="lead">
                <h2 class="lead-copy" style="color: aliceblue;">美脚スプレー式ファンデーション～Air Stocking&reg;</h2>
            </div>

            <div class="feature">
                <div class="feature-text">
                    <h2>履かないストッキング</h2>
                    <p>「エアーストッキング」は〜（中略）〜すぐに塗ることができます。</p>
                </div>
                <img src="{{ asset('images/other/main1.jpg') }}" alt="">
            </div>

            <div class="feature reverse">
                <div class="feature-text">
                    <h2>プレミアシルクとダイヤモンドレッグスの2タイプ</h2>
                    <p>「プレミアシルク」は〜（中略）〜のアイテムです。</p>
                </div>
                <img src="{{ asset('images/other/types.png') }}" alt="">
            </div>

            <div class="feature">
                <div class="feature-text">
                    <h2>5つの肌色から選べます</h2>
                    <p>美白色から日焼け色まで〜（中略）〜自然に仕上げます。</p>
                </div>
                <img src="{{ asset('images/other/color.gif') }}" alt="">
            </div>

            <div class="movie-bg">
                <div class="movie">
                    <h2>CONCEPT MOVIE</h2>
                    <video id="responsiveVideo" controls muted>
                        <source
                            src="https://m.media-amazon.com/images/S/al-jp-eb5039ce-f881/7b6da0ed-910c-4b6d-ade2-37d055e15396.mp4"
                            type="video/mp4">
                        お使いのブラウザは動画タグをサポートしていません。
                    </video>
                </div>
            </div>
        @endif

        {{-- 一般会員・法人会員 共通のLINE UP --}}
        <div class="line-up">
            <h2>LINE UP</h2>

            {{-- Premium Silk --}}
            <h3>PREMIUM SILK</h3>
            <div class="container">
                <ul class="product-list container">
                    @foreach ($premiumSilk as $product)
                        <li>
                            <div class="content">
                                <a href="{{ asset('product/airstocking/' . $product->id) }}">
                                    @if ($product->mainImage)
                                        <img src="{{  url('uploads/' . $product->mainImage->image_path) }}" alt="">
                                    @else
                                        <img src="{{ asset('images/noimage.png') }}" alt="画像なし">
                                    @endif
                                </a>
                                <p class="title">{{ $product->name }}</p>
                                <p class="price">¥{{ number_format($product->price) }}</p>
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <div class="mb-3">
                                        <label>数量：</label>
                                        <input type="number" name="quantity" value="1" min="1"
                                            class="form-control" style="width:100px">
                                    </div>
                                    <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Diamond Legs --}}
            <h3>DIAMOND LEGS</h3>
            <div class="container">
                <ul class="product-list container">
                    @foreach ($diamondLegs as $product)
                        <li>
                            <div class="content">
                                <a href="{{ asset('product/airstocking/' . $product->id) }}">
                                    @if ($product->mainImage)
                                        {{--<img src="{{ asset($product->mainImage->image_path) }}" alt="">--}}
                                        <img src="{{  url('uploads/' . $product->mainImage->image_path) }}" alt="">
                                    @else
                                        <img src="{{ asset('images/noimage.png') }}" alt="画像なし">
                                    @endif
                                </a>
                                <p class="title">{{ $product->name }}</p>
                                <p class="price">¥{{ number_format($product->price) }}</p>
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <div class="mb-3">
                                        <label>数量：</label>
                                        <input type="number" name="quantity" value="1" min="1"
                                            class="form-control" style="width:100px">
                                    </div>
                                    <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </main>



    @else
        
        <main class="main" style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 80vh; text-align: center;">
        <h2>{{ ucfirst($category) }} 商品一覧</h2>
        {{-- gelnail や wax など他のカテゴリ --}}
        <p>このカテゴリには現在登録されている商品がありません。</p>
        </main>
    @endif
@endsection
