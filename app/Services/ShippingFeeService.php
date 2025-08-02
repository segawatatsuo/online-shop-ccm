<?php

// app/Services/ShippingFeeService.php
// 送料計算サービス
namespace App\Services;

use App\Models\ShippingFee;

class ShippingFeeService
{
    public function getFeeByPrefecture(string $prefecture): int
    {
        $fee = ShippingFee::where('prefecture', $prefecture)->first();
        return $fee ? $fee->fee : 0; // 該当なしは0円
    }
}
