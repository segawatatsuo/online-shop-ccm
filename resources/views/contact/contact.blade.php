<p>お名前: {{ $data['name'] }}</p>
<p>メール: {{ $data['email'] }}</p>
<p>メッセージ:</p>
<p>{!! nl2br(e($data['message'])) !!}</p>
