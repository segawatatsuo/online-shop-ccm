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

                    const tokenResult = await card.tokenize({
                        amount: "100",
                        currencyCode: "USD",
                        intent: "CHARGE",
                        billingContact: {
                            familyName: "Yamada",
                            givenName: "Taro",
                            email: "taro@example.com",
                            country: "US",
                            city: "New York",
                            addressLines: ["123 Main St"],
                            postalCode: "94103",
                            phone: "1234567890"
                        }
                    });

                    if (tokenResult.status === "OK") {
                        const response = await fetch("/process-payment", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                token: tokenResult.token
                            })
                        });

                        const result = await response.json();
                        if (result.success) {
                            alert("決済成功！");
                        } else {
                            alert("エラー: " + result.message);
                        }
                    } else {
                        alert("トークンエラー");
                    }
                });

            } catch (e) {
                console.error("Squareの初期化でエラー:", e);
            }
        });
    </script>
</body>

</html>
