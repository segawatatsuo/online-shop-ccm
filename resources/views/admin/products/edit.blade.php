@extends('layouts.app')

@section('content')


    <h1>商品編集(edit)</h1>
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.products.form')
        <button type="submit">更新</button>
    </form>


    <h4>登録済み画像</h4>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        @foreach ($product->images as $image)
            <div style="text-align: center;">
                <img src="{{ asset('storage/' . $image->filename) }}" style="max-width:150px;"><br>
                @if ($image->is_main)
                    <span>メイン画像</span>
                @endif
                <form method="POST" action="{{ route('admin.product_images.destroy', $image->id) }}" onsubmit="return confirm('この画像を削除してよろしいですか？')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger mt-1">削除</button>
                </form>
            </div>
        @endforeach
    </div>
    
    




@endsection
