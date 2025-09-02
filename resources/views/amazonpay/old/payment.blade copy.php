<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Amazon Pay 決済ページ</title>
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
        }
        .payment-section {
            text-align: center;
            margin: 30px 0;
        }
        #AmazonPayButton {
            max-width: 100%;
            margin: 20px auto;
        }
        .error {
            color: #d32f2f;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Amazon Pay 決済</h1>
        
        @if(session('error'))
            <div class="error">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="payment-section">
            <h2>お支払い方法</h2>
            <p>Amazon Payで安全にお支払いいただけます。</p>
            
            <div id="AmazonPayButton"></div>
        </div>
    </div>

    <script src="{{ config('amazonpay.checkout_js_url') }}"></script>
    <script type="text/javascript">
        amazon.Pay.renderButton('#AmazonPayButton', {
            merchantId: '{{ $merchantId }}',
            ledgerCurrency: 'JPY',
            sandbox: {{ $sandbox ? 'true' : 'false' }},
            checkoutLanguage: 'ja_JP',
            productType: 'PayOnly',
            placement: 'Cart',
            buttonColor: 'Gold',
            createCheckoutSessionConfig: {
                payloadJSON: '{!! $payloadJson !!}',
                signature: '{{ $signature }}',
                publicKeyId: '{{ $publicKeyId }}'
            }
        });
    </script>
</body>
</html>