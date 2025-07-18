<div class="card">
    <div class="card-header">発送通知メールプレビュー</div>
    <div class="card-body">
        <h4>送信先: {{ $order->customer->email }}</h4>
        <h5>件名: {{ $subject }}</h5>
        <hr>
        <div style="border:1px solid #ccc; padding:10px;">
            {!! $body !!}
        </div>
        <hr>
        <div>
            <form method="POST" action="{{ route('admin.send-shipping-mail.send', $order->id) }}">
                @csrf
                <button type="submit" class="btn btn-success">この内容で送信する</button>
                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary">戻る</a>
            </form>
        </div>
    </div>
</div>
