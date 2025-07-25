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
    use App\Http\Controllers\OrderController;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\Log; // Logファサードをuseする

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
            Log::debug('PaymentController: processPaymentメソッド開始');
            Log::debug('PaymentController: リクエストデータ', $request->all());

            $request->validate([
                'token' => 'required|string',
                'amount' => 'required|integer|min:1',
            ]);

            $token = $request->input('token');
            $amount = $request->input('amount');

            $square_amount = $amount;
            $currency = 'JPY';

            try {
                Log::debug('PaymentController: SquareClient初期化前');
                $square_client = new SquareClient(
                    token: env('SQUARE_ACCESS_TOKEN'),
                    options: [
                        'baseUrl' => (env('SQUARE_ENVIRONMENT', 'sandbox') === 'sandbox')
                            ? Environments::Sandbox->value
                            : Environments::Production->value
                    ]
                );
                Log::debug('PaymentController: SquareClient初期化完了');
                Log::debug('PaymentController: Square Environment', ['env' => env('SQUARE_ENVIRONMENT', 'sandbox'), 'baseUrl' => $square_client->getConfig()->getBaseUrl()]);


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

                Log::debug('PaymentController: createPaymentRequest作成完了', ['sourceId' => $token, 'amount' => $square_amount, 'currency' => $currency]);
                Log::debug('PaymentController: Square API payments->create呼び出し前');
                $response = $square_client->payments->create($create_payment_request);
                Log::debug('PaymentController: Square API payments->create呼び出し後');

                $errors = $response->getErrors();
                $errors = $errors ?? [];

                if (empty($errors)) {
                    Log::info('PaymentController: Square決済成功');
                    $payment = $response->getPayment();
                    Log::debug('PaymentController: Square Payment Object', ['payment_id' => $payment->getId(), 'status' => $payment->getStatus()]);

                    // ここでOrderControllerのhogeメソッドを呼び出す
                    Log::debug('PaymentController: OrderController::hoge呼び出し前');
                    $orderController = new OrderController(app(\App\Services\CartService::class));
                    $orderSaveResult = $orderController->hoge(new Request());
                    Log::debug('PaymentController: OrderController::hoge呼び出し後', ['result' => $orderSaveResult->getStatusCode()]);

                    if ($orderSaveResult->getStatusCode() === 302 && $orderSaveResult->getTargetUrl() === route('order.complete')) {
                        Log::info('PaymentController: 支払い処理と注文情報の保存が成功し、注文完了画面へリダイレクト');
                        return response()->json([
                            'success' => true,
                            'message' => '支払い処理と注文情報の保存が成功しました。',
                            'payment' => $payment
                        ]);
                    } else {
                        Log::error('PaymentController: 支払い処理は成功したが、注文情報の保存に失敗', [
                            'payment_id' => $payment->getId(),
                            'order_save_result' => $orderSaveResult->getContent() // エラー内容をログに記録
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => '支払い処理は成功しましたが、注文情報の保存に失敗しました。',
                            'payment' => $payment
                        ], 500);
                    }

                } else {
                    $firstError = $errors[0];
                    $errorCode = $firstError->getCode();
                    $errorMessage = $this->getFriendlyErrorMessage($errorCode);
                    Log::error('PaymentController: Square決済失敗 (APIエラー)', [
                        'errorCode' => $errorCode,
                        'errorMessage' => $errorMessage,
                        'errors' => $errors
                    ]);

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

                Log::error('PaymentController: Square API Exception', [
                    'exception_message' => $e->getMessage(),
                    'errors' => $errors,
                    'stack_trace' => $e->getTraceAsString() // スタックトレースもログに
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errorDetail' => $e->getMessage(),
                    'errors' => $errors
                ], 500);
            } catch (\Exception $e) {
                Log::error('PaymentController: 予期せぬエラー', [
                    'exception_message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString() // スタックトレースもログに
                ]);

                return response()->json([
                    'success' => false,
                    'message' => '予期せぬエラーが発生しました。時間をおいて再度お試しください。',
                    'errorDetail' => $e->getMessage()
                ], 500);
            } finally {
                Log::debug('PaymentController: processPaymentメソッド終了');
            }
        }
    }
    