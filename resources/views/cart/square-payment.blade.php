@extends('layouts.app') {{-- 必要に応じて調整 --}}

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="text/javascript" src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
    <script type="text/javascript">
        // Square Application ID と Location ID は実際の値に置き換えてください
        const SQUARE_APP_ID = 'sandbox-sq0idb-FLpYRCd5CtAkwcfFupdDiQ';
        const SQUARE_LOCATION_ID = 'LDMBNMJX0HGH7';
    </script>


    <style>
        main.main {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 2rem;
            background-color: #f9f9f9;
            /* 背景色（お好みで） */
        }

        main.main form {
            width: 100%;
            max-width: 1200px;
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        main.main form div {
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        main.main form label {
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }

        main.main form input,
        main.main form textarea {
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        main.main form input:focus,
        main.main form textarea:focus {
            outline: none;
            border-color: #007bff;
        }

        main.main form button {
            background-color: #007bff;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        main.main form button:hover {
            background-color: #0056b3;
        }

        #payment-form {
            border: 1px solid #ccc;
            padding: 1rem;
            border-radius: 10px;
            background: #fefefe;
            margin-bottom: 1rem;
        }

        #pay-button {
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
            display: block;
            padding: 0.5rem;
            font-size: 1.2rem;
            border-radius: 10px;
            border: 1px solid #ffd814;
            background-color: #ffd814;
        }
    </style>
    <style>
        #pay-button {
            cursor: pointer;
        }
    </style>
@endsection



@section('content')
    <main class="main">
        <h1>お支払いフォーム</h1>

        <p>購入金額: <span id="display-amount">1000</span>円</p>

        <div id="payment-form"></div>
        <button id="pay-button" disabled>支払い</button>

        <div style="margin-top: 10px"><a href="https://squareup.com/jp/ja" target="_blank">当店はSquareオンライン決済を利用しています</a></div>
        <div style="margin-top: 10px"><img style="max-width: 400px"
                src="{{ asset('/images/card/PayPay_digital_logo_download_0818-04.png') }}"></div>
    </main>

    <div id="messages" style="margin-top: 20px;"></div>

    <script type="text/javascript">
        /*
                window.showError = function(message) {
                    const messagesDiv = document.getElementById('messages');
                    messagesDiv.innerHTML = `<p style="color: red;">${message}</p>`;
                };
                */
        window.showError = function(message) {
            Swal.fire({
                icon: 'error',
                title: 'エラーが発生しました',
                text: message,
                confirmButtonText: 'OK',
            });
        };

        window.showSuccess = function(message) {
            Swal.fire({
                icon: 'success',
                title: '完了しました',
                text: message,
                confirmButtonText: 'OK',
            });
        };



        window.showSuccess = function(message) {
            const messagesDiv = document.getElementById('messages');
            messagesDiv.innerHTML = `<p style="color: green;">${message}</p>`;
        };

        const friendlyErrorMessages = {
            // Square Web Payments SDKのエラーコード (tokenizeの結果)
            INVALID_CARD: "カード番号が無効です。入力内容をご確認ください。",
            INVALID_EXPIRATION: "有効期限が無効です。正しい日付を入力してください。",
            INVALID_CVV: "セキュリティコード（CVV）が無効です。入力内容をご確認ください。",
            INVALID_POSTAL_CODE: "郵便番号が無効です。入力内容をご確認ください。",
            UNACCEPTED_POSTAL_CODE: "この郵便番号は受け付けられません。",
            // サーバーサイドからのエラーコード (process-paymentの結果)
            GENERIC_DECLINE: "カードが承認されませんでした。別のカードをご利用ください。",
            FRAUD_SUSPECTED: "不正利用の可能性があるため、決済が拒否されました。",
            CARD_EXPIRED: "このカードは有効期限が切れています。",
            CVV_FAILURE: "セキュリティコード（CVV）が間違っている可能性があります。",
            AMOUNT_TOO_LARGE: "支払い金額が大きすぎます。カード会社にご確認ください。",
            INSUFFICIENT_FUNDS: "残高不足により支払いが拒否されました。",
            // その他の一般的なエラー
            UNKNOWN_ERROR: "不明なエラーが発生しました。再度お試しください。",
            DEFAULT: "お支払いに失敗しました。別の方法をお試しください。"
        };

        // エラーコードに対応する日本語メッセージを返す関数
        function getFriendlyErrorMessage(errorCode) {
            return friendlyErrorMessages[errorCode] || friendlyErrorMessages.DEFAULT;
        }

        async function initializeCard(payments) {
            const card = await payments.card();
            await card.attach('#payment-form'); // フォーム要素を挿入するID

            const payButton = document.getElementById('pay-button');
            payButton.disabled = false; // フォームが準備できたらボタンを有効化

            payButton.addEventListener('click', async function() {
                try {
                    const result = await card.tokenize();
                    if (result.status === 'OK') {
                        console.log(`Payment token: ${result.token}`);
                        const purchaseAmount = document.getElementById('display-amount').innerText;
                        await window.createPayment(result.token, parseInt(purchaseAmount));
                    } else {
                        // Square SDKが返すエラー（カード入力エラーなど）
                        let errorMessage = "カード情報の入力に問題があります。";
                        if (result.errors && result.errors.length > 0) {
                            // 最初の具体的なエラーコードを優先して表示
                            const errorCode = result.errors[0].code;
                            errorMessage = getFriendlyErrorMessage(errorCode);
                            console.error("Tokenization errors:", result.errors);
                        } else {
                            errorMessage = getFriendlyErrorMessage("UNKNOWN_ERROR");
                        }
                        window.showError(errorMessage);
                    }
                } catch (e) {
                    console.error("Error during tokenization setup or execution:", e);
                    window.showError("決済フォームの準備中にエラーが発生しました。再度お試しください。");
                }
            });
        }

        window.createPayment = async function(token, amount) {
            const dataJsonString = JSON.stringify({
                token: token,
                amount: amount,
            });

            try {
                const response = await fetch("/process-payment", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                    },
                    body: dataJsonString,
                });

                const data = await response.json();

                if (response.ok) {
                    window.showSuccess(data.message || "支払い処理が成功しました。");
                    window.location.href = "/thank-you";
                } else {
                    // サーバーサイドからのエラー
                    let errorMessage = "お支払いに失敗しました。";

                    if (data.errors && data.errors.length > 0) {
                        // Square APIからのエラーコードがある場合
                        const errorCode = data.errors[0].code || "UNKNOWN_ERROR";
                        errorMessage = getFriendlyErrorMessage(errorCode);
                    } else if (data.errorDetail) {
                        // 詳細なエラーメッセージがある場合
                        errorMessage = data.errorDetail;
                    } else if (data.message) {
                        // 一般的なメッセージがある場合
                        errorMessage = data.message;
                    } else {
                        // 特定のエラーメッセージがない場合
                        errorMessage = getFriendlyErrorMessage("DEFAULT");
                    }
                    window.showError(errorMessage);
                }
            } catch (error) {
                console.error("通信エラー:", error);
                window.showError("サーバーとの通信中にエラーが発生しました。再度お試しください。");
            }
        };

        // DOMContentLoaded で初期化
        document.addEventListener('DOMContentLoaded', async () => {
            if (!window.Square) {
                console.error('Square Web Payments SDKがロードされていません。');
                window.showError('決済機能を読み込めませんでした。ページの再読み込みをお試しください。');
                return;
            }

            try {
                const payments = Square.payments(SQUARE_APP_ID, SQUARE_LOCATION_ID);
                await initializeCard(payments);
            } catch (e) {
                console.error('Square Paymentsの初期化に失敗しました:', e);
                window.showError('決済フォームの初期化に失敗しました。再度お試しください。');
            }
        });
    </script>
@endsection
