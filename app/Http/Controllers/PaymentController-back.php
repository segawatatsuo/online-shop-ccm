<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Square\SquareClient;
use Square\Types\Money;
use Square\Payments\Requests\CreatePaymentRequest;
use Square\Types\Currency;
use Ramsey\Uuid\Uuid;
use App\Models\Order;
use Square\Environments;
use Square\Exceptions\SquareApiException;
use App\Http\Controllers\OrderController; // OrderControllerをuseする
use Illuminate\Support\Facades\Session; // Sessionファサードをuseする

class PaymentController extends Controller
{
    private const FRIENDLY_ERROR_MESSAGES = [
        'GENERIC_DECLINE' => 'カードが承認されませんでした。別のカードをご利用ください。',
        'CVV_FAILURE' => 'セキュリティコード（CVV）が間違っている可能性があります。',
        'CARD_EXPIRED' => 'このカードは有効期限が切れています。',
        'INSUFFICIENT_FUNDS' => '残高不足により支払いが拒否されました。',
        'INVALID_CARD_DATA' => 'カード情報が無効です。入力内容をご確認ください。',
        'AMOUNT_TOO_LARGE' => '支払い金額が大きすぎます。カード会社にご確認ください。',
        'TRANSACTION_REJECTED' => '取引が拒否されました。',
        'BAD_REQUEST' => 'リクエストが不正です。入力内容を確認してください。',
        'INTERNAL_SERVER_ERROR' => 'システムエラーが発生しました。時間をおいて再度お試しください。',
        'DEFAULT' => 'お支払いに失敗しました。別の方法をお試しください。'
    ];

    private function getFriendlyErrorMessage(string $errorCode): string
    {
        return self::FRIENDLY_ERROR_MESSAGES[$errorCode] ?? self::FRIENDLY_ERROR_MESSAGES['DEFAULT'];
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'amount' => 'required|integer|min:1',
        ]);

        $token = $request->input('token');
        $amount = $request->input('amount');

        $square_amount = $amount;
        $currency = 'JPY';

        try {
            $square_client = new SquareClient(
                token: env('SQUARE_ACCESS_TOKEN'),
                options: [
                    'baseUrl' => (env('SQUARE_ENVIRONMENT', 'sandbox') === 'sandbox')
                        ? Environments::Sandbox->value
                        : Environments::Production->value
                ]
            );

            $money = new Money();
            $money->setAmount($square_amount);
            $money->setCurrency($currency);

            $idempotency_key = Uuid::uuid4()->toString();

            $create_payment_request = new CreatePaymentRequest([
                'sourceId'       => $token,
                'idempotencyKey' => $idempotency_key,
                'amountMoney'    => $money,
                'autocomplete'   => true,
            ]);

            $response = $square_client->payments->create($create_payment_request);

            $errors = $response->getErrors();
            $errors = $errors ?? [];

            if (empty($errors)) {
                $payment = $response->getPayment();

                // ここでOrderControllerのhogeメソッドを呼び出す
                // hogeメソッドがRequestオブジェクトを期待しているので、空のRequestを渡すか、
                // hogeメソッドの引数を変更するかのどちらかになります。
                // 現在のhogeメソッドはセッションから情報を取得しているので、
                // ここでは空のRequestオブジェクトを渡しても問題ありません。
                $orderController = new OrderController(app(\App\Services\CartService::class)); // CartServiceをDI
                $orderSaveResult = $orderController->hoge(new Request()); // hogeメソッドを呼び出す

                // hogeメソッドの戻り値（リダイレクトレスポンスなど）を適切に処理
                // 例えば、成功したら注文完了画面へリダイレクト
                if ($orderSaveResult->getStatusCode() === 302 && $orderSaveResult->getTargetUrl() === route('order.complete')) {
                    return response()->json([
                        'success' => true,
                        'message' => '支払い処理と注文情報の保存が成功しました。',
                        'payment' => $payment
                    ]);
                } else {
                    // hogeメソッドでエラーが発生した場合の処理
                    // 例: データベース保存に失敗したが、決済は成功したケース
                    \Log::error('Order save failed after successful Square payment.');
                    return response()->json([
                        'success' => false,
                        'message' => '支払い処理は成功しましたが、注文情報の保存に失敗しました。',
                        'payment' => $payment // 支払い自体は成功しているので情報を返す
                    ], 500);
                }

            } else {
                $firstError = $errors[0];
                $errorCode = $firstError->getCode();
                $errorMessage = $this->getFriendlyErrorMessage($errorCode);

                Order::create([
                    'payment_token' => $token,
                    'amount' => $amount,
                    'currency' => $currency,
                    'status' => 'failed',
                    'square_response' => json_encode($errors),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => $errors
                ], 400);
            }
        } catch (SquareApiException $e) {
            $errors = $e->getErrors();
            $errorMessage = 'Square APIとの通信中にエラーが発生しました。';
            if (!empty($errors) && isset($errors[0])) {
                $errorCode = $errors[0]->getCode();
                $errorMessage = $this->getFriendlyErrorMessage($errorCode);
            }

            \Log::error('Square API Error: ' . $e->getMessage(), ['exception' => $e, 'errors' => $errors]);

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'errorDetail' => $e->getMessage(),
                'errors' => $errors
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Payment processing unexpected error: ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'message' => '予期せぬエラーが発生しました。時間をおいて再度お試しください。',
                'errorDetail' => $e->getMessage()
            ], 500);
        }
    }
}