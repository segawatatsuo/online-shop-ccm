<?php

namespace  App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;



class MypageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth'); // ログイン必須
    }

    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('orderDetails.product')->latest()->get(); // 過去の注文履歴も取得
        return view('mypage.index', compact('orders'));
    }

    public function show()
    {
        return view('mypage', ['user' => Auth::user()]);
    }

    public function edit()
    {
        $user = Auth::user();
        return view('mypage.edit', compact('user'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
        ]);

        $user = Auth::user();
        $user->update($request->only('name', 'address', 'phone'));

        return redirect()->route('mypage.edit')->with('success', 'プロフィールを更新しました。');
    }

    public function editPassword()
    {
        return view('mypage.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'new_password'          => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => '現在のパスワードが正しくありません']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('mypage.edit')->with('success', 'パスワードを変更しました。');
    }
}
