@extends('layouts.app')

@section('content')
    <h1>商品一覧</h1>
    <div class="row">
        @foreach ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        @if ($user)
                            <p class="card-text">会員価格: ¥{{ number_format($product->member_price) }}</p><!--会員価格-->
                        @else
                            <p class="card-text">価格: ¥{{ number_format($product->price) }}</p><!--ログインしてない場合価格-->
                        @endif

                        <!--route()メソッドにはname() メソッドを使って定義したルートの名前,順序付き配列を渡す-->
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary">詳細を見る</a>
                        @if ($product->mainImage)
                            <img src="{{ asset('storage/' . $product->mainImage->image_path) }}" alt="{{ $product->name }}"
                                style="max-width:200px;">
                        @else
                            <p>画像なし</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
