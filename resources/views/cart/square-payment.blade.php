@extends('layouts.app')

@section('title', 'トップページ')

@push('styles')
    {{-- _responsive.cssは本当は共通CSSだがtop-page.cssの後に読み込まないと崩れるため --}}
    <link rel="stylesheet" href="{{ asset('css/kakunin-page.css') }}">
    <link rel="stylesheet" href="{{ asset('css/_responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/square.css') }}">
@endpush

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="text/javascript" src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        // Square Application ID と Location ID は実際の値に置き換えてください
        const SQUARE_APP_ID = 'sandbox-sq0idb-FLpYRCd5CtAkwcfFupdDiQ';
        const SQUARE_LOCATION_ID = 'LDMBNMJX0HGH7';
    </script>
    
@endsection

@section('content')

    <main class="main">
        <div class="container">
            <h2 class="section-title">お支払いフォーム</h2>
            <p>お支払い金額：<span id="display-amount">{{ number_format($totalAmount) }}</span>円</p>
            <div id="payment-form"></div>
            <button id="pay-button" disabled>支払う</button>

            <div id="loading-overlay"
                style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); justify-content: center; align-items: center; z-index: 1000;">
                <div class="spinner"></div>
                <p>支払い処理中です。しばらくお待ちください...</p>
            </div>

            <div style="margin-top: 10px"><a href="https://squareup.com/jp/ja" target="_blank">当店はSquareオンライン決済を利用しています</a>
            </div>
            <div style="margin-top: 10px"><img style="max-width: 400px"
                    src="{{ asset('/images/card/PayPay_digital_logo_download_0818-04.png') }}"></div>
        </div>
    </main>
    <div id="messages" style="margin-top: 20px;"></div>

    <script type="text/javascript">
        window.showError = function(message) {
            Swal.fire({
                icon: 'error',
                title: 'エラーが発生しました',
                text: message,
                confirmButtonText: 'OK',
            });
        };

        window.showSuccess = function(message) {
            /*メッセージは出さない。ただし、180行目で呼び出しているので今後復活する可能性を考え残しておく
            Swal.fire({
                icon: 'success',
                title: '完了しました',
                text: message,
                confirmButtonText: 'OK',
            });
            */
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

        // initializeCard関数を定義
        async function initializeCard(payments) {
            try {
                const card = await payments.card();
                await card.attach('#payment-form');
                return card;
            } catch (error) {
                console.error('Card initialization failed:', error);
                throw error;
            }
        }

        // 支払い処理ロジックとSquare SDKの初期化を統合
        document.addEventListener('DOMContentLoaded', async () => {
            const payButton = document.getElementById('pay-button');
            const loadingOverlay = document.getElementById('loading-overlay');

            if (!window.Square) {
                console.error('Square Web Payments SDKがロードされていません。');
                window.showError('決済機能を読み込めませんでした。ページの再読み込みをお試しください。');
                return;
            }

            let payments;
            let card;

            try {
                // Square Payments SDKの初期化
                payments = Square.payments(SQUARE_APP_ID, SQUARE_LOCATION_ID);
                console.log('Square payments initialized:', payments);

                // カードフォームの初期化
                card = await initializeCard(payments);
                console.log('Card initialized:', card);

                // カードフォームが準備できたらボタンを有効化
                card.addEventListener('ready', function() {
                    console.log('Card is ready, enabling pay button');
                    payButton.disabled = false;
                });

                // カードフォームのバリデーション状態が変化したときにボタンの有効無効を制御
                card.addEventListener('change', function(event) {
                    console.log('Card change event:', event.detail);
                    if (event.detail.errors && event.detail.errors.length > 0) {
                        console.log('Card has errors, disabling button');
                        payButton.disabled = true;
                    } else {
                        console.log('Card has no errors, enabling button');
                        payButton.disabled = false;
                    }
                });

                // 5秒後に強制的にボタンを有効化（デバッグ用）
                setTimeout(() => {
                    console.log('Force enabling button after 5 seconds');
                    payButton.disabled = false;
                }, 5000);

            } catch (e) {
                console.error('Square Paymentsの初期化に失敗しました:', e);
                window.showError('決済フォームの初期化に失敗しました。再度お試しください。');
                return;
            }

            // 「支払う」ボタンクリック時のイベントリスナー
            payButton.addEventListener('click', async function() {
                // 支払い処理が開始される前にローディングアニメーションを表示
                loadingOverlay.style.display = 'flex';
                payButton.disabled = true; // 二重送信を防ぐためボタンを無効化

                try {
                    // 1. Square SDK でカード情報をトークン化
                    const result = await card.tokenize();

                    if (result.status === 'OK') {
                        const token = result.token;
                        console.log('Square Token:', token);

                        const purchaseAmountText = document.getElementById('display-amount')
                            .innerText;
                        const purchaseAmount = parseInt(purchaseAmountText.replace(/,/g, ''));

                        // 2. 取得したトークンと金額をサーバーに送信
                        // ここを修正します: /public/ を追加
                        const response = await fetch("public/process-payment", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({
                                token: token,
                                amount: purchaseAmount,
                            }),
                        });

                        const data = await response.json();

                        if (response.ok) {
                            window.showSuccess(data.message || "支払い処理が成功しました。");
                            window.location.href = "/order/complete";
                        } else {
                            let errorMessage = "お支払いに失敗しました。";
                            if (data.errors && data.errors.length > 0) {
                                const errorCode = data.errors[0].code || "UNKNOWN_ERROR";
                                errorMessage = getFriendlyErrorMessage(errorCode);
                            } else if (data.message) {
                                errorMessage = data.message;
                            }
                            window.showError(errorMessage);
                            console.error('Server error:', data);
                        }

                    } else {
                        let errorMessage = 'カード情報の入力に問題があります。';
                        if (result.errors && result.errors.length > 0) {
                            const errorCode = result.errors[0].code;
                            errorMessage = getFriendlyErrorMessage(errorCode);
                        }
                        window.showError(errorMessage);
                        console.error('Square Tokenization Error:', result.errors);
                    }
                } catch (error) {
                    console.error('Error during payment process:', error);
                    window.showError('通信エラーが発生しました。時間をおいて再度お試しください。');
                } finally {
                    loadingOverlay.style.display = 'none';
                    payButton.disabled = false;
                }
            });
        });
    </script>
    <!--</main>-->





    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 削除ボタンのイベント
            document.querySelectorAll('.remove-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const form = this.closest('form');

                    Swal.fire({
                        title: '削除してもよろしいですか？',
                        text: "この商品をカートから削除します。",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'はい、削除します',
                        cancelButtonText: 'キャンセル',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // フラッシュメッセージ SweetAlert2 表示(商品をカートに追加しました！は非表示)
            /*
             */
        });
    </script>


@endsection
