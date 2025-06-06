@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">


    <main class="main">

        <div class="container">

            <!--breadcrumbsパート-->
            <!--
            <div class="breadcrumbs">ホーム / エアーストッキング / 健康肌-テラコッタ / [健康肌][PS03] エアーストッキング プレミアシルク テラコッタ AirStocking
                Premier
                Silk
                120g Terra-cotta
            </div>
            -->


            <!--leftsideパート-->
            <div class="leftside">


                @if ($product->mainImage)
                    <div class="igm-box main-img js-main-img">
                        <img src="{{ asset($product->mainImage->image_path) }}" alt="Main Image" style="max-width:400px;">
                    </div>
                @endif


                <!--flex box-->
                <!--<div class="product-list">-->
                <ul class="product-list sub-img js-sub-img">

                    <li class="data-thumb current">
                        <img src="{{ asset($product->mainImage->image_path) }}" alt="">
                    </li>
                    <li class="data-thumb"><img src="{{ asset('/images/other/point/1.jpg') }}" alt=""></li>
                    <li class="data-thumb"><img src="{{ asset('/images/other/point/2.jpg') }}" alt=""></li>
                    <li class="data-thumb"><img src="{{ asset('/images/other/point/3.jpg') }}" alt=""></li>
                    <li class="data-thumb"><img src="{{ asset('/images/other/point/4.gif') }}" alt=""></li>
                    <li class="data-thumb"><img src="{{ asset('/images/other/point/5.gif') }}" alt=""></li>
                    <li class="data-thumb"><img src="{{ asset('/images/other/point/6.gif') }}" alt=""></li>
                    <li class="data-thumb"><img src="{{ asset('/images/other/point/7.jpg') }}" alt=""></li>
                </ul>
                <!--</div>-->

            </div>


            <!--rightsideパート-->
            <div class="rightside">

                <p class="title"><h1>{{ $product->name }}</h1></p>

                @if ($product->mainImage)
                    <div class="igm-point">
                        <img src="{{ asset('images/other/AirStocking_POINT123.jpg') }}" style="max-width:400px;"
                            alt="エアーストッキングのポイント">
                    </div>
                @endif

                {{--
                <h2>履かないストッキング・エアーストッキング®</h2>
                <p>
                    エアーストッキング®は日本で開発されたグローバルスタンダードのスプレーファンデーションです。エアーストッキングは「VOGUE」「ELLE」「ニューヨーク
                    タイムズ」などの海外メディアで絶賛され世界中のセレブ・モデルにも大人気！
                    世界中のセレブの美脚を飾ったスーパーレッグファンデーションは日本はもちろんアメリカ、イギリス、フランス、イタリアそして中東にも輸出されています。
                    国内販売100万本、欧米など累計販売300万本のスタンダード美脚スプレーです。【日本製】
                </p>

                <h3>3つのポイント</h3>

                <div class="point">Point1 履かない！ムレない！スプレーストッキング</div>
                <div class="text">
                    エアーストッキング®は「生脚はいやだけど、でもサンダルは 履きたいし、ペディキュアも魅せたい。」
                    ある女性のそんな一言から生まれました。エアーストッキング®は脚に直接スプレーするスプレーファンデーションなので優れたカバー力と耐久性で、毛穴やシミ、キズあとなどをしっかりカバーして気品あるセミマットに仕上げます。
                </div>

                <div class="point">Point2 ソフトフォーカスで自然な仕上がり</div>

                <div class="text">
                    エアーストッキング®はシルクヴェールを纏うようなソフトフォーカス効果で自然な美脚に仕上げます。絹を超微粒子化したシルクパウダー、茶エキスやシルクプロテインなどのトリートメント成分を配合汗し日焼けや乾燥などのダメージからお肌を守っり又水、こすれに強いウォータープルーフを実現しています。
                </div>

                <div class="point">Point3 エアーストッキング®は石鹸で落とせます</div>

                <div class="text">
                    エアーストッキング®は汗や水を弾くウォータープルーフ機能を持っていますが石鹸などで簡単に落とすことができます。汗や雨などは中性もしくは弱酸性です。エアーストッキング®は中性・弱酸性の汗や雨などは弾き化粧崩れしませんがアルカリ性の石鹸で容易に落とすことが出来ます。
                    ※ 石鹸はアルカリ性（JIS規格）です。
                </div>

                <div class="point">
                    ★タイプ：Premier Silk(プレミアシルク)★
                </div>
                <div class="text">
                    「エアーストッキングプレミアシルク」は、シルクを超微粒子化したスプレータイプの脚用ファンデーションです。
                    優れたカバー力と耐久性で、毛穴やシミ、キズあとなどをしっかりカバー。汗や水、こすれに強いウォータープルーフ&スティングカラー処方。気品あるセミマットな仕上がり。茶エキスやシルクプロテインなどトリートメント成分配合で日焼けや乾燥などのダメージからお肌を守ります。
                </div>


                @if ($user)
                    <div class="price">¥{{ number_format($product->member_price) }}<span class="tax">(税込)</span></div>
                    <!--会員価格-->
                @else
                    <div class="price">¥{{ number_format($product->price) }}<span class="tax">(税込)</span></div>
                    <!--ログインしてない場合価格-->
                @endif
                --}}


                



                <form method="POST" action="{{ route('cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-3">
                        <label>数量：</label>
                        <input type="number" name="quantity" value="1" min="1" class="form-control"
                            style="width:100px">
                    </div>

                    <button type="submit" class="a-button" style="border: none">カートに入れる</button>
                </form>


{!! $product->description !!}
            </div>
        </div>
    </main>
@endsection
