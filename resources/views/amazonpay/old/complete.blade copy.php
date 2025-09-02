<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amazon Pay 決済完了</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 48px;
            color: #4caf50;
            margin-bottom: 20px;
        }
        .success-message {
            color: #2e7d32;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .order-info {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }
        .back-button {
            background-color: #ff9800;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .back-button:hover {
            background-color: #f57c00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <div class="success-message">決済が完了しました</div>
        
        <div class="order-info">
            <h3>ご注文詳細</h3>
            <p><strong>メールアドレス:</strong> {{ $email }}</p>
            @if(isset($orderData['checkoutSessionId']))
                <p><strong>注文ID:</strong> {{ $orderData['checkoutSessionId'] }}</p>
            @endif
            <p><strong>決済日時:</strong> {{ now()->format('Y年m月d日 H:i:s') }}</p>
        </div>
        
        <p>ご注文ありがとうございました。<br>
        確認メールを送信いたします。</p>
        
        <a href="{{ url('/') }}" class="back-button">トップページに戻る</a>
    </div>
</body>
</html>