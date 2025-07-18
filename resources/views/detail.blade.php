@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/detail-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
@endpush

@section('content')
    <!-- メインコンテンツ -->
    <main class="main-content">
        <div class="product-detail fade-in">
            <!-- 左側 - 画像セクション -->
            <div class="product-images">
                <div class="main-image" onclick="openModal(this)">
                    <img src="https://images.unsplash.com/photo-1585306251707-a5b0df6b41c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="メイン商品画像" style="display: none;">
                    メイン商品画像
                </div>
                
                <div class="thumbnail-grid">
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="https://images.unsplash.com/photo-1585306251707-a5b0df6b41c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="詳細画像1" style="display: none;">
                        詳細画像1
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="詳細画像2" style="display: none;">
                        詳細画像2
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="詳細画像3" style="display: none;">
                        詳細画像3
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="https://images.unsplash.com/photo-1585306251707-a5b0df6b41c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="詳細画像4" style="display: none;">
                        詳細画像4
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="詳細画像5" style="display: none;">
                        詳細画像5
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="https://images.unsplash.com/photo-1585306251707-a5b0df6b41c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="詳細画像6" style="display: none;">
                        詳細画像6
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="詳細画像7" style="display: none;">
                        詳細画像7
                    </div>
                    <div class="thumbnail" onclick="openModal(this)">
                        <img src="https://images.unsplash.com/photo-1585306251707-a5b0df6b41c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="詳細画像8" style="display: none;">
                        詳細画像8
                    </div>
                </div>
            </div>



            
            <!-- 右側 - 商品情報セクション -->
            <div class="product-info">
                <h2 class="product-title">プレミアムシルクスプレー</h2>

                    <div class="product-price">
        <p>&#xA5;3,300<span class="tax-info">（税込）</span></p>
    </div>

                
                <div class="product-image-cut">
                    <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="商品イメージカット" style="display: none;">
                    商品イメージカット
                </div>

                <div class="purchase-section">
                    <div class="quantity-section">
                        <label class="quantity-label">数量:</label>
                        <input type="number" class="quantity-input" value="1" min="1">
                    </div>
                    <button class="add-to-cart">カートに入れる</button>
                </div>

                <div class="product-description">
                    <div class="description-section">
                        <h3 class="description-title">革新的なシルクテクノロジー</h3>
                        <p class="description-text">
                            最新のシルクエッセンス技術により、髪の毛一本一本をコーティングし、艶やかで滑らかな髪質を実現します。天然シルクプロテインが髪の内部まで浸透し、ダメージを修復しながら美しいツヤを与えます。
                        </p>
                    </div>

                    <div class="description-section">
                        <h3 class="description-title">持続する美しさ</h3>
                        <p class="description-text">
                            一度の使用で24時間効果が持続し、湿気や汗に負けない美しい髪を保ちます。紫外線からも髪を守り、カラーリングした髪の色落ちも防ぎます。毎日のスタイリングがより簡単になり、理想のヘアスタイルを長時間キープできます。
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- モーダル -->
    <div id="imageModal" class="modal">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        <div class="modal-content">
            <img class="modal-image" id="modalImage" src="" alt="">
        </div>
    </div>

@endsection
