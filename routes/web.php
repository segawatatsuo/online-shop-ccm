<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\ProductJaController;
use App\Http\Controllers\ProductImageJaController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\AdminRegisterController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AmazonPayTestController;
use App\Http\Controllers\AmazonPayController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\CustomRegisterController; // ここは残します
use App\Http\Controllers\Auth\CorporateRegisterController;
use PhpParser\Node\Stmt\Return_;
use App\Http\Controllers\Auth\VerificationController;
use App\Models\Order;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

use App\Admin\Controllers\MailPreviewController;

use Square\Environments;
use App\Http\Controllers\PageController;

// ホーム → 商品一覧にリダイレクト
//Route::get('/', fn() => redirect('/products'));

// トップページに index.blade.php を表示
Route::get('/', function () {
    return view('index'); // resources/views/index.blade.php
})->name('products.index');

// 管理者ログイン
Route::get('admin/login', [App\Http\Controllers\Admin\AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [App\Http\Controllers\Admin\AdminLoginController::class, 'login']);

// 商品（カテゴリ別一覧と詳細）
Route::prefix('product')->name('product.')->group(function () {
    // カテゴリ別商品一覧
    Route::get('{category}', [ProductJaController::class, 'category'])->name('category');
    // 商品詳細（例: /product/airstocking/123）
    Route::get('{category}/{id}', [ProductJaController::class, 'show'])->name('show');
});

// カート
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add', [CartController::class, 'add'])->name('add');
    Route::post('update', [CartController::class, 'update'])->name('update');
    Route::post('remove', [CartController::class, 'remove'])->name('remove');
    
    // Amazon Pay
    Route::get('show', [CartController::class, 'show'])->name('show');
    Route::post('checkout-session', [CartController::class, 'createCheckoutSession'])->name('checkout-session');
    Route::get('complete', [CartController::class, 'review'])->name('amazonpay.review');
    
    //Squareのカード入力画面
    Route::post('square-payment', [CartController::class, 'squarePayment'])->name('square-payment');

    Route::get('which-payment', [CartController::class, 'whichPayment'])->name('which-payment');
});

// 注文
Route::prefix('order')->name('order.')->group(function () {
    Route::get('create', [OrderController::class, 'create'])->name('create');
    Route::post('confirm', [OrderController::class, 'confirm'])->name('confirm');
    Route::post('storeOrder', [OrderController::class, 'storeOrder'])->name('storeOrder'); //store
    Route::get('complete', [OrderController::class, 'complete'])->name('complete');
    Route::post('hoge', [OrderController::class, 'hoge'])->name('hoge');
    
    Route::get('modify/{type}', [OrderController::class, 'modify'])->name('modify');
});

// ★★★ カスタムメール認証ルートを Auth::routes() より前に定義 ★★★
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 法人ユーザー向けのメール確認ルート
Route::get('/corporate/email/verify/{id}/{hash}', [VerificationController::class, '__invoke'])
    ->name('corporate.verification.verify'); // 'signed' ミドルウェアも削除

// 個人ユーザー向けのメール確認ルート
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, '__invoke'])
    ->name('verification.verify'); // 'signed' ミドルウェアも削除


Route::post('/email/resend', [VerificationController::class, 'resend'])
    ->middleware(['auth', 'throttle:6,1'])->name('verification.resend');

// 認証関連
// CustomRegisterControllerによるカスタム登録フローを優先するため、
// Auth::routes() で生成されるデフォルトの /register を無効にし、
// 他の認証機能（ログイン、パスワードリセット）のみを有効にします。
// ★★★ verify => false に変更してカスタム認証ルートを使用 ★★★
Auth::routes(['register' => false, 'verify' => false]);

Route::get('/register', [CustomRegisterController::class, 'showForm'])->name('register');
Route::post('/register/confirm', [CustomRegisterController::class, 'confirm'])->name('register.confirm');
Route::post('/register/store', [CustomRegisterController::class, 'store'])->name('register.store');

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// カスタム(法人取引会員)登録ルート
// routes/web.php
Route::get('/corporate/register', [CorporateRegisterController::class, 'showForm'])->name('corporate.register');
Route::post('/corporate/register/confirm', [CorporateRegisterController::class, 'confirm'])->name('corporate.register.confirm');
Route::post('/corporate/register', [CorporateRegisterController::class, 'store'])->name('corporate.register.store');

// 顧客マイページ（認証必須、メール認証済み必須）
Route::middleware(['auth', 'verified'])->prefix('mypage')->name('mypage.')->group(function () {
    Route::get('/', [MypageController::class, 'index'])->name('index');
    Route::get('edit', [MypageController::class, 'edit'])->name('edit');
    Route::post('update', [MypageController::class, 'update'])->name('update');
    Route::get('password', [MypageController::class, 'editPassword'])->name('password.edit');
    Route::post('password', [MypageController::class, 'updatePassword'])->name('password.update');
});

// 管理者ルート（ログイン必須）
Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::resource('products', AdminProductController::class);
    // 管理者登録（ポリシー使用）
    Route::get('register', [AdminRegisterController::class, 'create'])->middleware('can:admin')->name('register');
    Route::post('register', [AdminRegisterController::class, 'store'])->middleware('can:admin');
    // 商品画像削除
    Route::delete('product-images/{id}', [ProductImageJaController::class, 'destroy'])->name('product_images.destroy');
});

// ホーム画面（ログイン後のリダイレクト用）
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Square
Route::post('/process-payment', [PaymentController::class, 'processPayment']);

//AmazonPayのSDKハ゛ーシ゛ョン取得
Route::get('/amazonpay/version', [AmazonPayTestController::class, 'version']);
Route::get('/amazonpay/start', [AmazonPayController::class, 'redirectToAmazonPay'])->name('amazonpay.start');
Route::get('/amazonpay/return', [AmazonPayController::class, 'handleReturn'])->name('amazonpay.return');

// Laravel-Adminのルーティング
Encore\Admin\Facades\Admin::routes();

// 注文処理が終わったら(cartが空になったら)以下のページにアクセスしてきたらトップページにリダイレクトさせるミドルウェア用のルート
Route::middleware(['cart.not.empty', 'prevent.back.history'])->group(function () {
    Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
    Route::get('/order/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
    Route::get('/cart/square-payment', [CartController::class, 'square-payment'])->name('cart.square-payment');
});

//お問い合わせフォーム
Route::get('/contact', [ContactController::class, 'showForm'])->name('contact.form');
Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');
Route::get('/contact/complete', [ContactController::class, 'complete'])->name('contact.complete');

Route::get('/thank-you', function () {
    return view('thank-you');
})->name('order.thank-you');

Route::get('/kiyaku', function () {
    return view('kiyaku');
});
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});
Route::get('/tokutei', function () {
    return view('tokutei');
});

//法人取引会員登録でのメール送信しました確認画面
Route::get('/corporate/register/confirm_message', function () {
    return view('auth.confirm_message'); // 任意の Blade テンプレート
})->name('corporate.register.confirm_message');


// 法人ユーザー向けのメール再送信ルート
Route::post('/corporate/resend-verification', function () {
    $email = session('resent_email');

    if (!$email) {
        return redirect()->route('corporate.register')->withErrors(['error' => 'セッションが切れました。もう一度登録してください。']);
    }

    $user = User::where('email', $email)->first();

    if ($user && !$user->hasVerifiedEmail()) {
        event(new Registered($user)); // ← ここで再送
        return back()->with('status', '認証メールを再送信しました。');
    }

    return back()->withErrors(['error' => 'メール再送信に失敗しました。']);
})->name('corporate.verification.resend');


Route::get('/top', function () {
    return view('top');
});


//プライバシーポリシー
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy.policy');
//利用規約
Route::get('/rule', [PageController::class, 'rule'])->name('rule');
//特定商取引法に基づく表示
Route::get('/legal', [PageController::class, 'legal'])->name('legal');



/*
|--------------------------------------------------------------------------
| Amazon Pay Routes
|--------------------------------------------------------------------------
*/
/*
Route::prefix('amazon-pay')->name('amazon-pay.')->group(function () {

    Route::post('/payment', [AmazonPayController::class, 'payment'])
    ->name('payment');  // amazon-pay.payment
   
    // 決済セッション作成（金額確定後）
    Route::post('/create-session', [AmazonPayController::class, 'createPaymentSession'])->name('create-session');
    
    // 決済完了
    Route::get('/return', [AmazonPayController::class, 'completePayment'])->name('complete');
    
    // 決済キャンセル
    Route::get('/cancel', [AmazonPayController::class, 'cancelPayment'])->name('cancel');
    
    // エラーページ
    Route::get('/error', [AmazonPayController::class, 'errorPayment'])->name('error');

    // Webhook
    Route::post('/webhook', [AmazonPayController::class, 'webhook']);

});
*/
Route::prefix('amazon-pay')->name('amazon-pay.')->group(function () {
    Route::get('/payment', [AmazonPayController::class, 'showPayment'])->name('payment');
    Route::post('/create-session', [AmazonPayController::class, 'createPaymentSession'])->name('create-session');
    //Amazon が checkoutSessionId を持った状態で ここにリダイレクトします。
    Route::get('/complete', [AmazonPayController::class, 'complete'])->name('complete');

    Route::get('/cancel', [AmazonPayController::class, 'cancelPayment'])->name('cancel');
    Route::get('/error', [AmazonPayController::class, 'errorPayment'])->name('error');
    Route::get('/return', [AmazonPayController::class, 'amazonPayReturn'])->name('return');
    Route::post('/webhook', [AmazonPayController::class, 'webhook'])->name('webhook');

    Route::get('/review', [AmazonPayController::class, 'review'])->name('review');
});













Route::get('/ssl-test', function () {
    try {
        $response = Http::timeout(10)->get('https://example.com');
        return '✅ 通信成功: ステータスコード ' . $response->status();
    } catch (\Exception $e) {
        return '❌ 通信エラー: ' . $e->getMessage();
    }
});


Route::get('/square-api-test', function () {
    // .envから環境変数を取得
    $squareEnvironment = env('SQUARE_ENVIRONMENT', 'sandbox');
    $baseUrl = ($squareEnvironment === 'sandbox')
        ? Environments::Sandbox->value // Sandbox環境のURL
        : Environments::Production->value; // Production環境のURL

    // Square APIの適当なエンドポイント（例: /v2/locations）
    // 実際には認証なしではアクセスできないが、接続性テストには使える
    $testUrl = $baseUrl . '/v2/locations';

    // cURLを使って通信テスト
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true); // ヘッダーも取得
    curl_setopt($ch, CURLOPT_NOBODY, true); // ボディは取得しない (HEADリクエストのように)
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // タイムアウトを10秒に設定

    // cacert.pemのパスを明示的に指定
    $caCertPath = '/home/iiyama/online-shop-ccm/storage/ssl/cacert.pem';
    if (file_exists($caCertPath)) {
        curl_setopt($ch, CURLOPT_CAINFO, $caCertPath);
        curl_setopt($ch, CURLOPT_CAPATH, dirname($caCertPath)); // CAPATHも設定
    } else {
        return 'エラー: cacert.pemが見つかりません。パスを確認してください: ' . $caCertPath;
    }

    // 接続のデバッグ情報を出力
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $verbose = fopen('php://temp', 'rw+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $curlErrno = curl_errno($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    fclose($verbose);

    curl_close($ch);

    if ($curlError) {
        return 'Square APIへの通信エラーが発生しました。<br>' .
               'cURLエラー (' . $curlErrno . '): ' . $curlError . '<br>' .
               '詳細ログ:<pre>' . htmlspecialchars($verboseLog) . '</pre>';
    } else {
        return 'Square APIへの通信に成功しました。<br>' .
               'HTTPステータスコード: ' . $httpCode . '<br>' .
               '詳細ログ:<pre>' . htmlspecialchars($verboseLog) . '</pre>' .
               '（注意: 認証エラーは正常な接続を示します。通信自体が成功しているかを確認してください。）';
    }
});



// Admin画面での商品発送メールのプレビュー
// ルートをAdmin認証が必要なグループ内に配置
/*
Route::middleware(['admin.auth'])->group(function () {
    Route::get('admin/mail-preview/{orderId}', [MailPreviewController::class, 'preview'])
        ->name('admin.mail-preview')
        ->where('orderId', '[0-9]+'); // 数字のみ許可
});
*/

// Admin画面での商品発送メールのプレビュー
// ルートをAdmin認証が必要なグループ内に配置
Route::middleware(['admin.auth'])->group(function () {
    // 従来のビューベースプレビュー
    Route::get('admin/mail-preview/{orderId}', [MailPreviewController::class, 'preview'])
        ->name('admin.mail-preview')
        ->where('orderId', '[0-9]+'); // 数字のみ許可
    
    // テンプレートベースプレビュー
    Route::get('admin/mail-preview-template/{orderId}', [MailPreviewController::class, 'previewTemplate'])
        ->name('admin.mail-preview-template')
        ->where('orderId', '[0-9]+'); // 数字のみ許可
});

// または、admin認証ミドルウェアがない場合は
// Route::get('admin/mail-preview/{orderId}', [MailPreviewController::class, 'preview'])
//     ->name('admin.mail-preview')
//     ->where('orderId', '[0-9]+');
// Route::get('admin/mail-preview-template/{orderId}', [MailPreviewController::class, 'previewTemplate'])
//     ->name('admin.mail-preview-template')
//     ->where('orderId', '[0-9]+');