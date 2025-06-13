<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // uniqueルールで使用するため追加

class CorporateRegistrationRequest extends FormRequest
{
    /**
     * リクエストが許可されているか判断します。
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // ここをtrueに設定することで、このリクエストを認証されたユーザーでなくても許可します。
        // もし認証されたユーザーのみに許可したい場合は、認証ロジックをここに追加してください。
        return true;
    }


    //same_as_ordererが1の場合、order_sei等の値をdelivery_sei等にコピーする
    public function prepareForValidation()
    {
        if ($this->input('same_as_orderer') == '1') {
            $this->merge([
                'delivery_company_name' => $this->input('order_company_name'),
                'delivery_department' => $this->input('order_department'),
                'delivery_sei' => $this->input('order_sei'),
                'delivery_mei' => $this->input('order_mei'),
                'delivery_zip' => $this->input('order_zip'),
                //'delivery_email' => $this->input('order_email'),
                'delivery_phone' => $this->input('order_phone'),
                'delivery_add01' => $this->input('order_add01'),
                'delivery_add02' => $this->input('order_add02'),
                'delivery_add03' => $this->input('order_add03'),
            ]);
        }
    }




    /**
     * リクエストに適用するバリデーションルールを取得します。
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // 'same_as_orderer' の値を取得します。チェックボックスがチェックされている場合 '1'、
        // チェックされていない場合（hidden input のおかげで）'0' が返されます。
        // $sameAsOrderer = $this->input('same_as_orderer'); // この変数は直接使わないため削除

        return [
            // 基本認証情報
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')], // usersテーブルでユニークであることを確認
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 8文字以上、確認フィールドとの一致

            // 注文者情報（法人取引会員情報）
            'order_company_name' => ['required', 'string', 'max:255'],
            'order_department' => ['nullable', 'string', 'max:255'],//部署名
            'order_sei' => ['required', 'string', 'max:255'], // 姓
            'order_mei' => ['required', 'string', 'max:255'], // 名
            'order_phone' => ['required', 'string', 'regex:/^\d{2,4}-\d{2,4}-\d{3,4}$/', 'max:20'], // ハイフンを含む電話番号形式
            'homepage' => ['nullable', 'url', 'max:255'], // URL形式、任意
            //'order_email' => ['required', 'string', 'email', 'max:255'], // メール形式
            'order_zip' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], // 郵便番号形式
            'order_add01' => ['required', 'string', 'max:255'], // 都道府県
            'order_add02' => ['required', 'string', 'max:255'], // 市区町村
            'order_add03' => ['nullable', 'string', 'max:255'], // 市区町村以降の住所（任意）

            // お届け先情報（same_as_orderer が '0' の場合に必須）
            // 'same_as_orderer' が '0' (チェックされていない) の場合のみ以下のフィールドを必須にします。
            'same_as_orderer' => ['boolean'], // boolean 型であることを確認

            'delivery_company_name' => ['required_if:same_as_orderer,0', 'string', 'max:255'],
            'delivery_department' => ['nullable', 'string', 'max:255'],
            'delivery_sei' => ['required_if:same_as_orderer,0', 'string', 'max:255'],
            'delivery_mei' => ['required_if:same_as_orderer,0', 'string', 'max:255'],
            'delivery_phone' => ['required_if:same_as_orderer,0', 'string', 'regex:/^\d{2,4}-\d{2,4}-\d{3,4}$/', 'max:20'],
            /*'delivery_email' => ['required_if:same_as_orderer,0', 'string', 'email', 'max:255'],*/
            'delivery_zip' => ['required_if:same_as_orderer,0', 'string', 'regex:/^\d{3}-\d{4}$/'],
            'delivery_add01' => ['required_if:same_as_orderer,0', 'string', 'max:255'],
            'delivery_add02' => ['required_if:same_as_orderer,0', 'string', 'max:255'],
            'delivery_add03' => ['nullable', 'string', 'max:255'], // お届け先の市区町村以降の住所は任意
        ];
    }

    /**
     * 定義済みバリデーションルールのエラーメッセージを取得します。
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // 基本認証情報のエラーメッセージ
            'email.required' => 'メールアドレスは必ず入力してください。',
            'email.string' => 'メールアドレスは文字列で入力してください。',
            'email.email' => 'メールアドレスは有効な形式で入力してください。',
            'email.max' => 'メールアドレスは:max文字以内で入力してください。',
            'email.unique' => 'このメールアドレスは既に使用されています。',

            'password.required' => 'パスワードは必ず入力してください。',
            'password.string' => 'パスワードは文字列で入力してください。',
            'password.min' => 'パスワードは:min文字以上で入力してください。',
            'password.confirmed' => 'パスワードが確認用と一致しません。',

            // 注文者情報のエラーメッセージ
            'order_company_name.required' => '会社名は必ず入力してください。',
            'order_company_name.string' => '会社名は文字列で入力してください。',
            'order_company_name.max' => '会社名は:max文字以内で入力してください。',

            'order_sei.required' => 'ご担当者姓は必ず入力してください。',
            'order_sei.string' => 'ご担当者姓は文字列で入力してください。',
            'order_sei.max' => 'ご担当者姓は:max文字以内で入力してください。',

            'order_mei.required' => 'ご担当者名は必ず入力してください。',
            'order_mei.string' => 'ご担当者名は文字列で入力してください。',
            'order_mei.max' => 'ご担当者名は:max文字以内で入力してください。',

            'order_phone.required' => '電話番号は必ず入力してください。',
            'order_phone.string' => '電話番号は文字列で入力してください。',
            'order_phone.regex' => '電話番号は正しい形式（例: 03-000-0000）で入力してください。',
            'order_phone.max' => '電話番号は:max文字以内で入力してください。',

            'homepage.url' => 'ホームページURLは有効なURL形式で入力してください。',
            'homepage.max' => 'ホームページURLは:max文字以内で入力してください。',
            /*
            'order_email.required' => 'メールアドレスは必ず入力してください。',
            'order_email.string' => 'メールアドレスは文字列で入力してください。',
            'order_email.email' => 'メールアドレスは有効な形式で入力してください。',
            'order_email.max' => 'メールアドレスは:max文字以内で入力してください。',
            */
            'order_zip.required' => '郵便番号は必ず入力してください。',
            'order_zip.string' => '郵便番号は文字列で入力してください。',
            'order_zip.regex' => '郵便番号は正しい形式（例: 123-4567）で入力してください。',

            'order_add01.required' => '住所（都道府県）は必ず入力してください。',
            'order_add01.string' => '住所（都道府県）は文字列で入力してください。',
            'order_add01.max' => '住所（都道府県）は:max文字以内で入力してください。',

            'order_add02.required' => '住所（市区町村）は必ず入力してください。',
            'order_add02.string' => '住所（市区町村）は文字列で入力してください。',
            'order_add02.max' => '住所（市区町村）は:max文字以内で入力してください。',

            'order_add03.string' => '市区町村以降の住所は文字列で入力してください。',
            'order_add03.max' => '市区町村以降の住所は:max文字以内で入力してください。',

            // お届け先情報のエラーメッセージ
            'delivery_company_name.required_if' => 'お届け先が注文者情報と異なる場合、会社名は必ず入力してください。',
            'delivery_company_name.string' => 'お届け先の会社名は文字列で入力してください。',
            'delivery_company_name.max' => 'お届け先の会社名は:max文字以内で入力してください。',

            'delivery_sei.required_if' => 'お届け先が注文者情報と異なる場合、ご担当者姓は必ず入力してください。',
            'delivery_sei.string' => 'お届け先のご担当者姓は文字列で入力してください。',
            'delivery_sei.max' => 'お届け先のご担当者姓は:max文字以内で入力してください。',

            'delivery_mei.required_if' => 'お届け先が注文者情報と異なる場合、ご担当者名は必ず入力してください。',
            'delivery_mei.string' => 'お届け先のご担当者名は文字列で入力してください。',
            'delivery_mei.max' => 'お届け先のご担当者名は:max文字以内で入力してください。',

            'delivery_phone.required_if' => 'お届け先が注文者情報と異なる場合、電話番号は必ず入力してください。',
            'delivery_phone.string' => 'お届け先の電話番号は文字列で入力してください。',
            'delivery_phone.regex' => 'お届け先の電話番号は正しい形式（例: 090-999-0000）で入力してください。',
            'delivery_phone.max' => 'お届け先の電話番号は:max文字以内で入力してください。',
            /*
            'delivery_email.required_if' => 'お届け先が注文者情報と異なる場合、メールアドレスは必ず入力してください。',
            'delivery_email.string' => 'お届け先のメールアドレスは文字列で入力してください。',
            'delivery_email.email' => 'お届け先のメールアドレスは有効な形式で入力してください。',
            'delivery_email.max' => 'お届け先のメールアドレスは:max文字以内で入力してください。',
            */
            'delivery_zip.required_if' => 'お届け先が注文者情報と異なる場合、郵便番号は必ず入力してください。',
            'delivery_zip.string' => 'お届け先の郵便番号は文字列で入力してください。',
            'delivery_zip.regex' => 'お届け先の郵便番号は正しい形式（例: 123-4567）で入力してください。',

            'delivery_add01.required_if' => 'お届け先が注文者情報と異なる場合、住所（都道府県）は必ず入力してください。',
            'delivery_add01.string' => 'お届け先の住所（都道府県）は文字列で入力してください。',
            'delivery_add01.max' => 'お届け先の住所（都道府県）は:max文字以内で入力してください。',

            'delivery_add02.required_if' => 'お届け先が注文者情報と異なる場合、住所（市区町村）は必ず入力してください。',
            'delivery_add02.string' => 'お届け先の住所（市区町村）は文字列で入力してください。',
            'delivery_add02.max' => 'お届け先の住所（市区町村）は:max文字以内で入力してください。',

            'delivery_add03.string' => 'お届け先の市区町村以降の住所は文字列で入力してください。',
            'delivery_add03.max' => 'お届け先の市区町村以降の住所は:max文字以内で入力してください。',
        ];
    }
}
