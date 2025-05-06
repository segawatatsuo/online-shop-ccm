@extends('layouts.app')

@section('content')
    <h1>商品一覧（管理画面）</h1>

    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">新規作成</a>

    @foreach ($products as $product)
        <div style="border: 1px solid #ccc; padding: 10px; margin:10px 0;">
            <h3>{{ $product->name }}（{{ $product->price }}円）</h3>
            <p>{{ $product->description }}</p>
            <p>カテゴリ: {{ $product->category->name ?? '-' }}</p>

            @if ($product->mainImage)
                <div>
                    <img src="{{ asset('storage/' . $product->mainImage->filename) }}" style="max-width:100px;">
                </div>
            @endif

            <a href="{{ route('admin.products.edit', $product) }}">編集</a>
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display:inline;">
                @csrf @method('DELETE')
                <button onclick="return confirm('削除しますか？')">削除</button>
            </form>




            
        </div>
    @endforeach

    {{ $products->links() }}
@endsection
