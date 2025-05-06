<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Square\SquareClient;
use Square\Models\Money;
use Square\Models\CreatePaymentRequest;

class PaymentController extends Controller
{
    public function show()
    {
        return view('payment');
    }

    public function process(Request $request)
    {
        $client = new SquareClient([
            'accessToken' => env('SQUARE_ACCESS_TOKEN'),
            'environment' => 'sandbox',
        ]);

        $paymentsApi = $client->getPaymentsApi();

        $money = new Money();
        $money->setAmount(100); // 100円
        $money->setCurrency('JPY');

        $paymentRequest = new CreatePaymentRequest(
            $request->input('token'),
            uniqid(),
            $money
        );

        $response = $paymentsApi->createPayment($paymentRequest);

        if ($response->isSuccess()) {
            return response()->json(['success' => true]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response->getErrors()[0]->getDetail() ?? '不明なエラー'
            ]);
        }
    }
}
