<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User; // Userモデルをインポート
use Illuminate\Support\Facades\URL; // URLファサードをインポート (必要に応じて)
use Illuminate\Auth\Access\AuthorizationException; // 必要に応じて

class VerificationController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth'); // これは引き続きコメントアウトまたは削除
        // $this->middleware('signed')->only('__invoke'); // ここも削除します。手動で署名を検証します
        $this->middleware('throttle:6,1')->only('resend'); // resend メソッドのみ throttle を適用
    }

    // __invoke メソッドの引数を変更
    public function __invoke(Request $request, $id, $hash) // $id と $hash を直接受け取る
    {
        Log::info('--- VerificationController __invoke START ---');
        Log::info('Request URL: ' . $request->fullUrl());
        Log::info('URL ID: ' . $id);
        Log::info('URL Hash: ' . $hash);

        // ユーザーをIDで検索
        $user = User::find($id);

        // ユーザーが存在しない、またはハッシュが一致しない、または署名が無効な場合
        if (!$user || !hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            Log::warning('Verification failed: User not found or hash mismatch for ID ' . $id);
            // 署名が無効な場合の処理 (LaravelのSignedMiddlewareと同様のチェック)
            // URL::hasValidSignature() は Request の署名を検証します
            if (! URL::hasValidSignature($request)) {
                 throw new AuthorizationException('メール認証リンクが無効です。');
            }
             throw new AuthorizationException('メール認証に失敗しました。');
        }

        // ユーザーが既にメール認証済みの場合
        if ($user->hasVerifiedEmail()) {
            Log::info('User already verified: ' . $user->email);
            // 既に認証済みの場合は、そのままリダイレクト
            if ($user->user_type === 'corporate') {
                return redirect('/admin/login')->with('message', 'メールは既に認証済みです。管理者ログインページです。');
            } else {
                return redirect('/login')->with('message', 'メールは既に認証済みです。');
            }
        }

        // メール認証を実行
        $user->markEmailAsVerified();
        Log::info('メール認証が完了しました。ユーザーID: ' . $user->id . ', Email: ' . $user->email);
        Log::info('Email verified at (after markEmailAsVerified): ' . ($user->email_verified_at ?? 'null'));

        // リダイレクト処理
        if ($user->user_type === 'corporate') {
            Log::info('法人ユーザーと判定されました。/admin/login にリダイレクトします');
            return redirect('/admin/login')->with('verified', true)->with('message', 'メール認証が完了しました。管理者ログインページです。');
        } else {
            Log::info('個人ユーザーまたはユーザータイプがcorporateではありません。/login にリダイレクトします');
            return redirect('/login')->with('verified', true)->with('message', 'メール認証が完了しました。');
        }
    }

    /**
     * 再送信処理
     */
    public function resend(Request $request)
    {
        // resend は auth ミドルウェアが必要な場合があるため、一旦このまま
        // もし resend でも null エラーが出るなら、ここも手動認証を検討
        if (!$request->user()) {
            return redirect()->route('login')->withErrors(['error' => '再送信するにはログインが必要です。']);
        }

        if ($request->user()->hasVerifiedEmail()) {
            // 法人ユーザーの場合
            if ($request->user()->user_type === 'corporate') {
                return redirect('/admin/login');
            }
            // 個人ユーザーの場合
            return redirect('/login');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }
}