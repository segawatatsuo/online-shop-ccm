@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/kakunin-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')
    <!-- メインコンテンツ -->
<main class="main">
    <div class="container">
        <h2 class="section-title">ご注文内容の確認</h2>

        <div class="info-card">
            <div class="card-header">
                <h3>ご注文者情報</h3>
            </div>
            <div class="card-body grid-layout">
                <div><strong>氏名:</strong> 瀬川 達男</div>
                <div><strong>メール:</strong> segawa@lookingfor.jp</div>
                <div><strong>電話番号:</strong> 09091496802</div>
                <div><strong>郵便番号:</strong> 206-0823</div>
                <div class="grid-full"><strong>住所:</strong> 東京都 稲城市平尾 1-2-3</div>
            </div>
        </div>

        <div class="info-card">
            <div class="card-header">
                <h3>お届け先情報</h3>
            </div>
            <div class="card-body grid-layout">
                <div><strong>氏名:</strong> 瀬川 達男</div>
                <div><strong>メール:</strong> segawa@lookingfor.jp</div>
                <div><strong>電話番号:</strong> 09091496802</div>
                <div><strong>郵便番号:</strong> 206-0823</div>
                <div class="grid-full"><strong>住所:</strong> 東京都 稲城市平尾 1-2-3</div>
            </div>
        </div>

        <div class="info-card">
            <div class="card-header">
                <h3>配送情報</h3>
            </div>
            <div class="card-body grid-layout">
                <div><strong>配送希望日:</strong> (指定なし)</div>
                <div><strong>配送時間:</strong> なし</div>
                <div class="grid-full"><strong>ご要望他:</strong> (特になし)</div>
            </div>
        </div>

        <div class="info-card">
            <div class="card-header">
                <h3>ご注文商品</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>商品名</th>
                                <th class="text-center">数量</th>
                                <th class="text-right">小計</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>エアーストッキングプレミアムシルク 120G テラコッタ</td>
                                <td class="text-center">1</td>
                                <td class="text-right">3,300円</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-right">合計金額</th>
                                <th class="text-right">3,300円</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="button-area">
            <a href="https://shop.ccmedico.com/cart" class="btn btn-secondary">戻る</a>
            <form action="https://shop.ccmedico.com/cart/square-payment" method="POST" class="d-inline-block">
                <input type="hidden" name="_token" value="4XthwELzA1TLmYf2NT77AIeVy1PTd">
                <button type="submit" class="btn btn-primary">お支払いへ進む</button>
            </form>
        </div>
    </div>
</main>
@endsection
