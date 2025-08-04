<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Setting;

class PageController extends Controller
{
    //プライバシーポリシーコンテンツ
    public function privacyPolicy()
    {
        $privacyContent = Setting::getValue('privacy-policy', '準備中です');
        return view('privacy-policy', compact('privacyContent'));
    }

    //利用規約コンテンツ
    public function rule()
    {
        $ruleContent = Setting::getValue('rule', '準備中です');
        return view('rule', compact('ruleContent'));
    }

    //特定商取引法に基づく表示
    public function legal()
    {
        $legalContent = Setting::getValue('legal', '準備中です');
        return view('legal', compact('legalContent'));
    }
}
