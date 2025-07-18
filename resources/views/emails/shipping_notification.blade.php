<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        /* 簡単なスタイルを記述しても良いですが、凝ったデザインはMailableクラスでHTMLを生成する方が良い */
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { width: 90%; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #eee; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; }
        .footer { margin-top: 30px; font-size: 0.9em; color: #777; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        {!! nl2br(e($body)) !!} {{-- 改行とエスケープを考慮して表示 --}}
    </div>
</body>
</html>