<?php

// app/Http/Controllers/AmazonPayTestController.php

namespace App\Http\Controllers;
use App\Services\AmazonPayService;

class AmazonPayTestController extends Controller
{
    protected $amazonPay;

    public function __construct(AmazonPayService $amazonPay)
    {
        $this->amazonPay = $amazonPay;
    }

    public function version()
    {
        return response()->json([
            'sdk_version' => $this->amazonPay->getSDKVersion(),
        ]);
    }
}

