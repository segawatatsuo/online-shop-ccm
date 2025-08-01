{{-- resources/views/emails/dynamic_mail_layout.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    {{-- 必要に応じて、すべてのメールに適用したい共通のCSSなどをここに追加できます --}}
    {{-- ただし、メールクライアントの互換性を考慮し、DBに保存するHTML側で
         インラインスタイルを記述する方が確実です --}}
</head>
<body>
    {{-- $body には、DBから読み込まれ、プレースホルダーが置換済みの完全なHTMLが入っています --}}
    {!! $body !!}
</body>
</html>