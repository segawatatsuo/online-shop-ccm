<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CorporateRegistrationRequest; // 法人登録用
use App\Http\Requests\IndividualRegistrationRequest; // ここを追加: 個人登録用
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CorporateCustomer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Registered;

class CorporateRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showForm()
    {
        //dd("showForm");
        return view('auth.corporate-register');
    }

    /**
     * 法人取引会員登録の確認ページを表示します。
     *
     * @param  \App\Http\Requests\CorporateRegistrationRequest  $request
     * @return \Illuminate\View\View
     */
    public function confirm(CorporateRegistrationRequest $request)
    {
        $input = $request->validated();
        //dd($input);

        $request->session()->put('corporate_register_data', $input);
        //dd(session('corporate_register_data'));
        return view('auth.corporate-confirm', ['input' => $input]);
    }

    public function store(Request $request)
    {
        // セッションから登録データを取得
        $input = $request->session()->get('corporate_register_data');

        if (!$input) {
            return redirect()->route('corporate.register');
        }

        DB::beginTransaction();

        try {
            // 1. ユーザーアカウント作成
            $user = User::create([
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'user_type' => 'corporate',
            ]);

            //dd($input);

            // 2. 法人情報作成
            // お届け先情報が注文者情報と同じ場合、お届け先データを注文者データからコピー
            if (isset($input['same_as_orderer']) && $input['same_as_orderer'] == '1') {
                $delivery_company_name = $input['order_company_name'];
                $delivery_department = $input['order_department'] ?? null;
                $deliverySei = $input['order_sei'];
                $deliveryMei = $input['order_mei'];
                $deliveryPhone = $input['order_phone'];
                //$deliveryEmail = $input['email'];
                $deliveryZip = $input['order_zip'];
                $deliveryAdd01 = $input['order_add01'];
                $deliveryAdd02 = $input['order_add02'];
                $deliveryAdd03 = $input['order_add03'];
            } else {
                // same_as_orderer が '0' の場合、または存在しない場合はお届け先データを使用
                $delivery_company_name = $input['delivery_company_name'] ?? null;
                $delivery_department = $input['delivery_department'] ?? null;
                $deliverySei = $input['delivery_sei'] ?? null;
                $deliveryMei = $input['delivery_mei'] ?? null;
                $deliveryPhone = $input['delivery_phone'] ?? null;
                //$deliveryEmail = $input['email'] ?? null;
                $deliveryZip = $input['delivery_zip'] ?? null;
                $deliveryAdd01 = $input['delivery_add01'] ?? null;
                $deliveryAdd02 = $input['delivery_add02'] ?? null;
                $deliveryAdd03 = $input['delivery_add03'] ?? null;
            }



            CorporateCustomer::create([
                'user_id' => $user->id,
                'order_company_name' => $input['order_company_name'],
                'order_department' => $input['order_department'] ?? null,
                'order_sei' => $input['order_sei'],
                'order_mei' => $input['order_mei'] ?? null,
                'order_phone' => $input['order_phone'] ?? null,
                'homepage' => $input['homepage'] ?? null,
                'email' => $input['email'],

                'order_zip' => $input['order_zip'],
                'order_add01' => $input['order_add01'] ?? null,
                'order_add02' => $input['order_add02'] ?? null,
                'order_add03' => $input['order_add03'] ?? null,

                'same_as_orderer' => $input['same_as_orderer'],

                'delivery_company_name' => $delivery_company_name,
                'delivery_department' => $delivery_department,
                'delivery_sei' => $deliverySei,
                'delivery_mei' => $deliveryMei,
                'delivery_phone' => $deliveryPhone,
                //'delivery_email' => $deliveryEmail,
                'delivery_zip' => $deliveryZip,
                'delivery_add01' => $deliveryAdd01,
                'delivery_add02' => $deliveryAdd02,
                'delivery_add03' => $deliveryAdd03,

                'discount_rate' => 0, // 初期値は0%、管理者が後で設定
                'is_approved' => true, // 初期状態は承認
            ]);

            DB::commit();

            // 登録イベントをディスパッチ
            event(new Registered($user));

            // ← ここでメールアドレスをセッションに保存（再送時に使う）
            $request->session()->put('resent_email', $input['email']);

            // セッションデータを削除
            $request->session()->forget('corporate_register_data');

            // 承認待ちメッセージと共にリダイレクト
            /*
            return redirect()->route('verification.notice')
                ->with('status', '法人登録が完了しました。メール認証後、管理者による承認をお待ちください。');
            */
            return redirect()->route('corporate.register.confirm_message');

            
        } catch (\Exception $e) {
            DB::rollback();
            // エラーログ出力 (開発時に役立ちます)
            // \Log::error('法人登録エラー: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => '登録処理中にエラーが発生しました。予期せぬエラーが発生しました。時間をおいて再度お試しください。']);
        }
    }

    // 既存の個人ユーザー登録用（元のコントローラーを残す場合）
    public function showIndividualForm()
    {
        return view('auth.register');
    }

    /**
     * 個人ユーザー登録の確認処理
     *
     * @param  \App\Http\Requests\IndividualRegistrationRequest  $request // ここを修正
     * @return \Illuminate\View\View
     */
    public function confirmIndividual(IndividualRegistrationRequest $request) // ここを修正
    {
        // IndividualRegistrationRequest がバリデーションを自動的に行います。
        $input = $request->validated(); // バリデート済みのデータを取得

        $request->session()->put('register_data', $input);

        return view('auth.confirm', ['input' => $input]);
    }

    // 個人ユーザー登録の保存処理（元のstoreメソッドをリネーム）
    public function storeIndividual(Request $request)
    {
        $input = $request->session()->get('register_data');

        if (!$input) {
            return redirect()->route('register');
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'postal_code' => $input['postal_code'] ?? null,
            'address' => $input['address'] ?? null,
            'phone' => $input['phone'] ?? null,
            'user_type' => 'individual',
        ]);

        event(new Registered($user));
        $request->session()->forget('register_data');

        return redirect()->route('verification.notice')
            ->with('status', '登録が完了しました。メールを確認してアカウントを有効化してください。');
    }
}
