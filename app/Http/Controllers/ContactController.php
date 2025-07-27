<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;


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

        // メール送信
        Mail::to('your@email.com')->send(new ContactMail($request->all()));

        return redirect()->route('contact.complete');
    }

    public function complete()
    {
        return view('contact.complete');
    }
}
