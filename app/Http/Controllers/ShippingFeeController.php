<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ShippingFee;

class ShippingFeeController extends Controller
{
    public function calculateShipping($prefecture)
    {
        $fee = ShippingFee::where('prefecture', $prefecture)->value('fee');

        if ($fee === null) {
            $fee = 1000; // 例: デフォルト送料
        }

        return $fee;
    }
}
