<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered; // Registeredイベントを使用

class CustomRegisterController extends Controller
{
    // コンストラクタでミドルウェアを設定すると、未認証ユーザーが登録ページにアクセスするのを防げます。
    // 今回は登録ページなので不要ですが、他の認証済みユーザー向けのコントローラでは有用です。
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showForm()
    {
        return view('auth.register');
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 以下、登録フォームに追加されているフィールドのバリデーションも忘れずに
            'postal_code' => 'required|string|max:10', // 例
            'address' => 'required|string|max:255', // 例
            'phone' => 'required|string|max:20', // 例
        ]);

        $input = $request->all();
        $request->session()->put('register_data', $input);


        return view('auth.confirm', ['input' => $input]);
    }

    public function store(Request $request)
    {
        // セッションから登録データを取得
        $input = $request->session()->get('register_data');

        if (!$input) {
            // セッションデータがない場合は登録フォームへリダイレクト
            return redirect()->route('register');
        }

        // ここでバリデーションを再度行うことも可能ですが、confirmで既に実施済みであれば不要な場合もあります。
        // セキュリティのため、再度バリデーションをかけるのが望ましいですが、今回は省略します。

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'postal_code' => $input['postal_code'] ?? null, // register.blade.php にある項目を追加
            'address' => $input['address'] ?? null, // register.blade.php にある項目を追加
            'phone' => $input['phone'] ?? null, // register.blade.php にある項目を追加
        ]);

        // ユーザー作成後、Registeredイベントをディスパッチ
        // これにより、メール認証の通知が送信されます。
        event(new Registered($user));

        // ユーザーをログインさせる場合
        // Auth::login($user); // メール認証前に自動ログインさせたい場合はコメント解除

        // セッションデータを削除
        $request->session()->forget('register_data');

        // ユーザーが登録後、メール認証を促すページへリダイレクト
        // Laravel UIのデフォルトでは、registered イベント後に自動的に認証メールが送信され、
        // ユーザーはログイン状態になるか、メール認証を促すメッセージが表示されるページにリダイレクトされます。
        // ここでは、デフォルトのLaravel UIの登録完了時のリダイレクト挙動に合わせるか、
        // メール認証待ちであることを明確に伝えるページにリダイレクトします。

        // デフォルトでは、Registeredイベントが発行されると、AuthenticateUsersトレイトによって
        // メール認証待ちの画面にリダイレクトされることがあります。
        // 明示的にリダイレクト先を指定する場合は、以下のようにします。
        return redirect()->route('verification.notice')->with('status', '登録が完了しました。メールを確認してアカウントを有効化してください。');
        // もしくは、ログインページにリダイレクトして、メール認証のメッセージを表示
        // return redirect()->route('login')->with('status', '登録が完了しました。メールを確認してアカウントを有効化してください。');
    }
}