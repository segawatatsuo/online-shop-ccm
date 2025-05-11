<?php

namespace App\Http\Controllers;

use App\Services\AmazonPayService;

class AmazonPayController extends Controller
{
    public function redirectToAmazonPay(AmazonPayService $amazonPayService)
    {
        $checkoutSession = $amazonPayService->createCheckoutSession(route('amazonpay.return'));
        $amazonPayUrl = $checkoutSession['webCheckoutDetails']['amazonPayRedirectUrl'];
        return redirect()->away($amazonPayUrl); // ← これが正解
    }

    public function handleReturn()
    {
        return '決済完了（テスト）';
    }
}
