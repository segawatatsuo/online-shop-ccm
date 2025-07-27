<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>お問い合わせ内容</title>
</head>
<body>
    <h2>お問い合わせ内容</h2>
    <p><strong>お名前:</strong> {{ $data['name'] }}</p>
    <p><strong>メールアドレス:</strong> {{ $data['email'] }}</p>
    <p><strong>メッセージ:</strong><br>{{ nl2br(e($data['message'])) }}</p>
</body>
</html>
