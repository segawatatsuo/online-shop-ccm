<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use App\Models\CompanyInfo;

class ContactController extends Controller
{
    public function showForm()
    {
        return view('contact.form');
    }

    public function submitForm(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'message' => 'required|string|max:1000',
        ]);

        // CompanyInfoモデルを使ってメールアドレス取得
        $toEmail = CompanyInfo::where('key', 'contact-mail')->value('value');

        // メール送信
        if ($toEmail) {
            Mail::to($toEmail)->send(new ContactMail($request->all()));
        }

        return redirect()->route('contact.complete');
    }

    public function complete()
    {
        return view('contact.complete');
    }
}
