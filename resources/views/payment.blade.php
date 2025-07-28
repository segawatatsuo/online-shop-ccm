<!DOCTYPE html>
<html>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="text/javascript" src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
</head>

<body>
    <form id="card-payment">
        <div id="card"></div>
        <button type="submit">支払う</button>
    </form>

<!--セッションからアドレスと合計金額を取得 -->
<script>
    const address = @json(session('address'));
    const totalAmount = @json(session('total'));
</script>


    <script>
        const appId = "{{ env('SQUARE_APP_ID') }}";
        const locationId = "{{ env('SQUARE_LOCATION_ID') }}";

        document.addEventListener("DOMContentLoaded", async function() {
            try {
                const payments = Square.payments(appId, locationId);
                const card = await payments.card();
                await card.attach("#card");
                console.log("カードフォームが正しく初期化されました");

                const form = document.getElementById("card-payment");
                form.addEventListener("submit", async (event) => {
                    event.preventDefault();

                    // 注意: amountとcurrencyCodeは実際の購入金額と通貨に合わせるべきです。
                    // PHP側ではJPYを使用しているので、ここもJPYに合わせるのが適切です。
                    const tokenResult = await card.tokenize({
                        amount: "100", // この金額はテスト用です。実際は動的に設定してください。
                        currencyCode: "JPY", // PHP側がJPYなので、ここもJPYに合わせるのが適切です。
                        intent: "CHARGE",
                        billingContact: {
                            familyName: address.order_sei,
                            givenName: address.order_mei,
                            email: address.order_email,
                            country: "JP",
                            city: address.order_add01,
                            addressLines:  [address.order_add02, address.order_add03].filter(Boolean),
                            postalCode: address.order_zip,
                            phone: address.order_phone
                        }
                    });

                    if (tokenResult.status === "OK") {
                        // ここを修正します: /public/ を追加
                        const response = await fetch("/public/process-payment", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                token: tokenResult.token,
                                // ここに決済金額も渡すように修正してください。
                                // 例: amount: 100 // 実際の金額を動的に取得
                                amount: totalAmount
                            })
                        });

                        // レスポンスがOKでない場合もエラーを適切に処理
                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error("サーバーからのエラーレスポンス:", errorText);
                            alert("通信エラーが発生しました。サーバー応答: " + response.status);
                            return; // 以降の処理を中断
                        }

                        const result = await response.json();
                        if (result.success) {
                            alert("決済成功！");
                            // 成功時のリダイレクトなど
                            // window.location.href = "/order/complete"; // 例
                        } else {
                            alert("エラー: " + result.message);
                        }
                    } else {
                        alert("トークンエラー: " + (tokenResult.errors ? JSON.stringify(tokenResult.errors) : "不明なエラー"));
                    }
                });

            } catch (e) {
                console.error("Squareの初期化でエラー:", e);
                alert("決済ページの初期化に失敗しました。時間をおいて再度お試しください。");
            }
        });
    </script>
</body>

</html>
