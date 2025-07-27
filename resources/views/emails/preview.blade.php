{{-- resources/views/emails/preview.blade.php --}}

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>
<body>
    <h2>{{ $subject }}</h2>
    <hr>
    {!! nl2br(e($body)) !!}
</body>
</html>
