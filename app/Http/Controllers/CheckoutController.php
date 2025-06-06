<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Square\Exceptions\ApiException;
use Square\Models\CreatePaymentRequest;
#use Square\Models\Money;
use Square\SquareClient;
use Square\Environments;
use Square\Types\Money;

class CheckoutController extends Controller
{
    private $squareClient;

    public function __construct(SquareClient $squareClient)
    {
        $this->squareClient = $squareClient;
    }

    public function showPaymentForm()
    {
        return view('checkout.payment_form', [
            'square_application_id' => config('app.square_application_id'),
            'square_location_id' => config('app.square_location_id'),
        ]);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'nonce' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:JPY', // 日本円の場合
        ]);

        $nonce = $request->input('nonce');
        $amount = $request->input('amount');
        $currency = $request->input('currency');
        $locationId = config('app.square_location_id');

        // idempotency_keyの生成
        // 冪等性キーは、ネットワークエラーなどでリクエストが重複しても、
        // 処理が一度だけ実行されることを保証するためのユニークな値です。
        // データベースに決済ログを保存する際に、このキーも保存して重複チェックに利用できます。
        $idempotencyKey = uniqid(); // 本番環境ではより堅牢な方法で生成することをお勧めします

        try {
            $paymentsApi = $this->squareClient->getPaymentsApi();

            $amountMoney = new Money();
            $amountMoney->setAmount((int) ($amount)); // 金額は最小単位（例: 1円 = 1）で設定
            $amountMoney->setCurrency($currency);

            $createPaymentRequest = new CreatePaymentRequest(
                $nonce,
                $idempotencyKey,
                $amountMoney
            );
            $createPaymentRequest->setLocationId($locationId);
            // その他のオプション（例: 注文ID, カスタマーID, 課金理由など）を設定できます
            // $createPaymentRequest->setOrderId('YOUR_ORDER_ID');
            // $createPaymentRequest->setCustomerId('YOUR_CUSTOMER_ID');
            // $createPaymentRequest->setNote('Description of the purchase');

            $response = $paymentsApi->createPayment($createPaymentRequest);

            if ($response->isSuccess()) {
                $payment = $response->getResult()->getPayment();

                // 決済成功時の処理
                // 例: データベースに決済情報を保存、注文ステータスを更新、ユーザーにメール送信など
                // $payment->getId() で決済IDを取得できます
                // $payment->getStatus() で決済ステータスを取得できます

                return response()->json([
                    'message' => 'Payment successful',
                    'payment_id' => $payment->getId(),
                    'status' => $payment->getStatus(),
                    // 他の必要な情報
                ]);
            } else {
                $errors = $response->getErrors();
                // エラーログを記録
                \Log::error('Square Payment Error:', ['errors' => $errors]);

                return response()->json([
                    'message' => 'Payment failed',
                    'errors' => collect($errors)->map(function ($error) {
                        return $error->getDetail();
                    })->toArray(),
                ], 400); // 400 Bad Request または適切なHTTPステータス
            }

        } catch (ApiException $e) {
            // Square APIからの例外をキャッチ
            \Log::error('Square API Exception:', [
                'message' => $e->getMessage(),
                'category' => $e->getCategory(),
                'code' => $e->getCode(),
                'errors' => $e->getErrors(),
            ]);

            return response()->json([
                'message' => 'Square APIエラーが発生しました。',
                'details' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // その他の予期せぬ例外をキャッチ
            \Log::error('Unexpected Payment Error:', ['message' => $e->getMessage()]);

            return response()->json([
                'message' => '予期せぬエラーが発生しました。',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
