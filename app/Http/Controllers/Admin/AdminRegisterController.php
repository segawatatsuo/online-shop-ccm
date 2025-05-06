<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreAdminRequest;

class AdminRegisterController extends Controller
{
    public function create()
    {
        return view('admin.register');
    }

    //public function store(Request $request)
    public function store(StoreAdminRequest $request)
    {

        /*****
        Laravelの Form Request クラスは、Illuminate\Http\Requestを継承しているので、基本的なリクエスト処理（$request->input() や $request->file() など）も全く同じように使えます。そのためリクエストクラス(StoreAdminRequest.php)でバリデーションをするように変更したので$requestを引数に渡す必要はなくなります
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        *****/

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
        ]);

        return redirect()->route('admin.register')->with('success', '管理者を作成しました。');
    }
}
