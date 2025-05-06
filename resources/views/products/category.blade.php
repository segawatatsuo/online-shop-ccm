{{--
@extends('layouts.app')

@section('content')
    <h2>{{ ucfirst($category) }} の商品一覧</h2>

    @foreach ($groupedProducts as $classification => $products)
        <h3>{{ $classification }}</h3>
        <div class="product-list">
            @foreach ($products as $product)
                <div class="product-item">
                    <a href="{{ route('product.show', ['category' => $category, 'id' => $product->id]) }}">
                        <h4>{{ $product->name }}</h4>

                    </a>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
--}}

{{-- 
@extends('layouts.app')

@section('content')
    <h2>{{ ucfirst($category) }} 商品一覧</h2>


    <section class="premium-silk">
        <h3>Premium Silk</h3>
        <p>こちらはエアーストッキングの中でも最高品質の Premium Silk シリーズです。</p>

        <div class="product-grid">
            @foreach ($premiumSilk as $product)
                <div class="product-item">
                    <a href="{{ route('product.show', ['category' => $category, 'id' => $product->id]) }}">
                    <h4>{{ $product->name }}</h4>

                    </a>
                </div>
            @endforeach
        </div>
    </section>


    <section class="diamond-legs mt-8">
        <h3>Diamond Legs</h3>
        <p>脚元に輝きを与える Diamond Legs シリーズをご紹介します。</p>

        <div class="product-grid">
            @foreach ($diamondLegs as $product)
                <div class="product-item">
                    <a href="{{ route('product.show', ['category' => $category, 'id' => $product->id]) }}">
                    <h4>{{ $product->name }}</h4>

                    </a>
                </div>
            @endforeach
        </div>
    </section>
@endsection
--}}

@extends('layouts.app')

@section('content')
    @if ($category === 'airstocking')
        <main class="main">

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
                    <p>「エアーストッキング」は「生足はいやだけど、でもサンダルは履きたいし、ペディキュアも魅せたい」というある女性の一言から生まれました。シルクパウダーで自然な仕上がりスプレー式美脚ファンデーションです。汗や雨などの中性・酸性領域では落ちにくく石鹸などのアルカリ性領域で落ちるようになっています。スプレー式なのでムラなくすぐに塗ることができます。
                    </p>
                </div>
                <img src="{{ asset('images/other/main1.jpg') }}" alt="">

            </div>

            <div class="feature reverse">
                <div class="feature-text">
                    <h2>プレミアシルクとダイヤモンドレッグスの2タイプ</h2>
                    <p>「プレミアシルク」は、超微粒子化シルクを配合したスプレータイプの脚用ファンデーションで、高いカバー力と耐久性を持ち、ウォータープルーフ仕様。トリートメント成分配合で肌を保護し、セミマットに仕上がります。「ダイヤモンドレッグス」は、ダイヤモンド0.1カラットとグリッターを配合したプロ向けスプレーファンデーションで、美脚をゴージャスに演出。ミスユニバースファイナリストも愛用するステージ向けのアイテムです。
                    </p>
                </div>
                <img src="{{ asset('images/other/types.png') }}" alt="">

            </div>


            <div class="feature">
                <div class="feature-text">
                    <h2>5つの肌色から選べます</h2>
                    <p>美白色から日焼け色まで5つの肌色から選べます。
                        カラーは「色白肌LNライトナチュラル」「普通肌Nナチュラル」「健康肌Tテラコッタ」「褐色肌Bブロンズ」「小麦肌Cココ」の5色からお選びください。
                        エアーストッキングは1本で脚だけでなく手・デコルテ・背中なども自然に仕上げます。</p>
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



            <div class="line-up">
                <h2>LINE UP</h2>

                <div class="container">

                    <img class="head-img" src="{{ asset('images/PremireSilk/PremireSilk-Banner.png') }}" alt="PremireSilk">

                    <div class="item1">

                        <a href="{{ asset('product/airstocking/1') }}"><img src="{{ asset('images/PremireSilk/PS1.jpg') }}"
                                alt=""></a>
                        <p class="title">ライトナチュラル</p>
                        <p class="price">\3,300</p>
                        <!--
                        <a href="./cart.html">
                            <div class="a-button">カートに入れる</div>
                        </a>
                        -->
                        <form method="POST" action="{{ route('cart.add') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="1">
                            <div class="mb-3">
                                <label>数量：</label>
                                <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
                            </div>
                    
                            <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                        </form>

                    </div>

                    <li class="item2">
                        <div class="content">
                            <a href="{{ asset('product/airstocking/2') }}"><img
                                    src="{{ asset('images/PremireSilk/PS2.jpg') }}" alt=""></a>
                            <p class="title">ナチュラル</p>
                            <p class="price">\3,300</p>
                            <!--<a href="./cart.html">-->
                                <!--<div class="a-button">カートに入れる</div></a>-->
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="2">
                                    <div class="mb-3">
                                        <label>数量：</label>
                                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
                                    </div>
                            
                                    <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                                </form>


                        </div>
                    </li>

                    <li class="item3">
                        <div class="content">
                            <a href="{{ asset('product/airstocking/3') }}"><img
                                    src="{{ asset('images/PremireSilk/PS3.jpg') }}" alt=""></a>
                            <p class="title">テラコッタ</p>
                            <p class="price">\3,300</p>
                            <!--<a href="./cart.html">-->
                                <!--<div class="a-button">カートに入れる</div></a>-->
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="3">
                                    <div class="mb-3">
                                        <label>数量：</label>
                                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
                                    </div>
                            
                                    <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                                </form>
                        </div>
                    </li>

                    <li class="item4">
                        <div class="content">
                            <a href="{{ asset('product/airstocking/4') }}"><img
                                    src="{{ asset('images/PremireSilk/PS4.jpg') }}" alt=""></a>
                            <p class="title">ブロンズ</p>
                            <p class="price">\3,300</p>
                            <!--<a href="./cart.html">-->
                                <!--<div class="a-button">カートに入れる</div></a>-->
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="4">
                                    <div class="mb-3">
                                        <label>数量：</label>
                                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
                                    </div>
                            
                                    <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                                </form>
                        </div>
                    </li>

                    <li class="item5">
                        <div class="content">
                            <a href="{{ asset('product/airstocking/5') }}"><img
                                    src="{{ asset('images/PremireSilk/PS5.jpg') }}" alt=""></a>
                            <p class="title">ココ</p>
                            <p class="price">\3,300</p>
                            <!--<a href="./cart.html">-->
                                <!--<div class="a-button">カートに入れる</div></a>-->
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="5">
                                    <div class="mb-3">
                                        <label>数量：</label>
                                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
                                    </div>
                            
                                    <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                                </form>
                        </div>
                    </li>

                </div>




                <div class="container">

                    <img class="head-img" src="{{ asset('images/DiamondLegs/DiamondLegs-Banner.png') }}" alt="PremireSilk">
                    <div class="item1">
                        <img src="{{ asset('images/DiamondLegs/DL1.jpg') }}" alt="">
                        <p class="title">ライトナチュラル</p>
                        <p class="price">\4,400</p>
                            <!--<a href="./cart.html">-->
                                <!--<div class="a-button">カートに入れる</div></a>-->
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="6">
                                    <div class="mb-3">
                                        <label>数量：</label>
                                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
                                    </div>
                            
                                    <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                                </form>
                    </div>

                    <li class="item2">
                        <div class="content">

                            <img src="{{ asset('images/DiamondLegs/DL2.jpg') }}" alt="">
                            <p class="title">ナチュラル</p>
                            <p class="price">\4,400</p>
                            <!--<a href="./cart.html">-->
                                <!--<div class="a-button">カートに入れる</div></a>-->
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="7">
                                    <div class="mb-3">
                                        <label>数量：</label>
                                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
                                    </div>
                            
                                    <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                                </form>
                        </div>
                    </li>

                    <li class="item3">
                        <div class="content">
                            <img src="{{ asset('images/DiamondLegs/DL3.jpg') }}" alt="">
                            <p class="title">テラコッタ</p>
                            <p class="price">\4,400</p>
                            <form method="POST" action="{{ route('cart.add') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="8">
                                <div class="mb-3">
                                    <label>数量：</label>
                                    <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
                                </div>
                        
                                <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                            </form>
                        </div>
                    </li>

                    <li class="item4">
                        <div class="content">
                            <img src="{{ asset('images/DiamondLegs/DL4.jpg') }}" alt="">
                            <p class="title">ブロンズ</p>
                            <p class="price">\4,400</p>
                            <form method="POST" action="{{ route('cart.add') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="9">
                                <div class="mb-3">
                                    <label>数量：</label>
                                    <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
                                </div>
                        
                                <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                            </form>
                        </div>
                    </li>

                    <li class="item5">
                        <div class="content">
                            <img src="{{ asset('images/DiamondLegs/DL5.jpg') }}" alt="">
                            <p class="title">ココ</p>
                            <p class="price">\4,400</p>
                            <form method="POST" action="{{ route('cart.add') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="10">
                                <div class="mb-3">
                                    <label>数量：</label>
                                    <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px">
                                </div>
                        
                                <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                            </form>
                        </div>
                    </li>

                </div>




            </div>

        </main>
    @else
        <h2>{{ ucfirst($category) }} 商品一覧</h2>
        {{-- gelnail や wax など他のカテゴリ --}}
        <p>このカテゴリには現在登録されている商品がありません。</p>
    @endif
@endsection
