@php
    use App\Models\OrderItem;
    use App\Models\ProductJa;

    // 商品IDごとに注文件数と注文個数（数量）を集計
    $popular = OrderItem::select('product_id',
            \DB::raw('COUNT(*) as count'),       // 注文件数（何回注文されたか）
            \DB::raw('SUM(quantity) as total_quantity') // 注文個数の合計
        )
        ->groupBy('product_id')
        ->orderByDesc('count')
        ->take(5)
        ->get();

    // 商品名を取得してマッピング
    $popularProducts = $popular->map(function ($item, $index) {
        $product = \App\Models\ProductJa::find($item->product_id);
        return [
            'rank' => $index + 1,
            'name' => $product ? $product->name : '不明',
            'count' => $item->count,
            'quantity' => $item->total_quantity,
        ];
    });
@endphp

<ol>
    @foreach($popularProducts as $product)
        <li>
            {{ $product['name'] }}
            （{{ $product['count'] }}件 /
            {{ $product['quantity'] }}個）
        </li>
    @endforeach
</ol>

