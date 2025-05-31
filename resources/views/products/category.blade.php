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

                <h3>PREMIUM SILK</h3>
                <div class="container">
                    <ul class="product-list container">
                        @foreach ($premiumSilk as $product)
                            <li>
                                <div class="content">
                                    <a href="{{ asset('product/airstocking/' . $product->id) }}">
                                        @if ($product->mainImage)
                                        <img src="{{ asset($product->mainImage->image_path)  }}" alt="">
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

                <h3>DIAMOND LEGS</h3>
                <div class="container">
                    <ul class="product-list container">
                        @foreach ($diamondLegs as $product)
                            <li>
                                <div class="content">
                                    <a href="{{ asset('product/airstocking/' . $product->id) }}">
                                        @if ($product->mainImage)
                                        <img src="{{ asset($product->mainImage->image_path)  }}" alt="">
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
