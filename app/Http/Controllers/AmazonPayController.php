<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Amazon\Pay\API\Client;
use App\Services\AmazonPayService;

class AmazonPayController extends Controller
{
    protected $amazonPayService;

    public function __construct(AmazonPayService $amazonPayService)
    {
        $this->amazonPayService = $amazonPayService;
    }

    /**
     * 支払いページを表示
     */
    public function showPayment()
    {
        return view('amazonpay.payment');
    }

    /**
     * 決済セッションを作成
     */
    public function createSession(Request $request)
    {
        /*
        $request->validate([
            'amount' => 'required|numeric|min:1|max:1000000',
        ], [
            'amount.required' => '金額を入力してください。',
            'amount.numeric' => '金額は数値で入力してください。',
            'amount.min' => '金額は1円以上で入力してください。',
            'amount.max' => '金額は1,000,000円以下で入力してください。',
        ]);
        */
        try {
            $amount = $request->input('amount');
            $paymentData = $this->amazonPayService->createSession($amount);

            $paymentData['amount'] = $amount;
            return view('amazonpay.payment_confirm', $paymentData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '決済の準備中にエラーが発生しました。')->withInput();
        }
    }


    /**
     * 決済完了処理
     */
    public function complete(Request $request)
    {
        $amazonCheckoutSessionId = $request->get('amazonCheckoutSessionId');
        
        if (empty($amazonCheckoutSessionId)) {
            return redirect()->route('payment.error')->with('error', 'セッションIDが無効です。');
        }

        try {
            // セッションから金額を取得（セキュリティのため）
            $amount = session('payment_amount', '100');
            $result = $this->amazonPayService->completePayment($amazonCheckoutSessionId, $amount);
            
            return view('amazonpay.complete', [
                'email' => $result['email'],
                'amount' => $amount,
                'orderData' => $result
            ]);
        } catch (\Exception $e) {
            \Log::error('AmazonPay決済エラー: ' . $e->getMessage());
            return redirect()->route('amazon-pay.error')->with('error', '決済処理中にエラーが発生しました。');
        }
    }

    /**
     * 決済キャンセル処理
     */
    public function cancelPayment()
    {
        return view('amazonpay.cancel');
    }

    /**
     * エラーページ
     */
    public function errorPayment()
    {
        return view('amazonpay.error');
    }
}