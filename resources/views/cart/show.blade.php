<!-- resources/views/cart/show.blade.php -->


<h2>カート</h2>
<ul>
    @foreach ($cart['items'] as $item)
        <li>{{ $item['name'] }} - ￥{{ $item['price'] }} × {{ $item['qty'] }}</li>
    @endforeach
</ul>
<p>お名前：{{ $cart['customer_name'] }}</p>

<!-- Amazon Pay SDK -->
<!--<script src="https://static-fe.pay.amazon.jp/checkout.js"></script>-->
<script src="https://static-fe.payments-amazon.com/checkout.js"></script>

<div id="AmazonPayButton"></div>


<script>
    async function initializeAmazonPay() {
        const response = await fetch('{{ route('cart.checkout-session') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.checkoutSessionId) {
            amazon.Pay.renderButton('#AmazonPayButton', {
                
                checkoutSessionId: data.checkoutSessionId,
                ledgerCurrency: 'JPY',
                // Amazonに遷移する方式
                createCheckoutSessionConfig: {
                    payloadJSON: data.payloadJSON ?? '',
                    signature: data.signature ?? '',
                    publicKeyId: '{{ env('AMAZON_PAY_PUBLIC_KEY_ID') }}'
                }
                /*
                merchantId: 'A2MQAIFB5IWHUJ',
                createCheckoutSession: {
                    url: './Services/AmazonPayService.php',
                },
                sandbox: true, // dev environment
                ledgerCurrency: 'JPY', // Amazon Pay account ledger currency
                checkoutLanguage: 'ja_JP', // render language
                productType: 'PayAndShip', // checkout type
                // productType: 'PayOnly', // payment type
                placement: 'Cart' // button placement
                */

            });
        } else {
            alert('Amazon Payの初期化に失敗しました');
            console.error(data);
        }
    }

    initializeAmazonPay();
</script>
