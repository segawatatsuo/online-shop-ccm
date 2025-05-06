@extends('layouts.app')

@section('content')

    <h1>カートの中身</h1>

    @if (empty($cart))
        <p>カートは空です。</p>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'カートが空です',
                    text: 'お買い物を続けてください',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        </script>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>商品名</th>
                    <th>価格</th>
                    <th>数量</th>
                    <th>小計</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

                @php $total = 0; @endphp

                @foreach ($cart as $item)
                    @php
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    @endphp
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>¥{{ number_format($item['price']) }}</td>
                        <td>
                            <form method="POST" action="{{ route('cart.update') }}" class="d-flex">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                    class="form-control me-2" style="width:80px">
                                <button class="btn btn-sm btn-primary">更新</button>
                            </form>
                        </td>
                        <td>¥{{ number_format($subtotal) }}</td>
                        <td>
                            <form method="POST" action="{{ route('cart.remove') }}" class="remove-form">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                                <button type="button" class="btn btn-sm btn-danger remove-btn">削除</button>
                            </form>

                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <h4>合計: ¥{{ number_format($total) }}</h4>
        <a href="{{ route('order.confirm') }}" class="btn btn-success">注文へ進む</a>
    @endif

@endsection

@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 削除ボタンのイベント
            document.querySelectorAll('.remove-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const form = this.closest('form');

                    Swal.fire({
                        title: '削除してもよろしいですか？',
                        text: "この商品をカートから削除します。",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'はい、削除します',
                        cancelButtonText: 'キャンセル',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // フラッシュメッセージ SweetAlert2 表示(商品をカートに追加しました！は非表示)
            @if (session('success') && session('success') !== '商品をカートに追加しました！')
                Swal.fire({
                    icon: 'success',
                    title: '完了！',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 1800
                });
            @endif
        });
    </script>
@endpush
