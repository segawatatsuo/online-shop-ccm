<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndividualRegistrationRequest extends FormRequest
{
    /**
     * リクエストが許可されているか判断します。
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // 認証されたユーザーでなくてもフォーム送信を許可
    }

    /**
     * リクエストに適用するバリデーションルールを取得します。
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'postal_code' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/', 'max:10'], // 郵便番号形式
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\d{2,4}-\d{2,4}-\d{3,4}$/', 'max:20'], // 電話番号形式
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
            'name.required' => 'お名前は必ず入力してください。',
            'name.string' => 'お名前は文字列で入力してください。',
            'name.max' => 'お名前は:max文字以内で入力してください。',

            'email.required' => 'メールアドレスは必ず入力してください。',
            'email.string' => 'メールアドレスは文字列で入力してください。',
            'email.email' => 'メールアドレスは有効な形式で入力してください。',
            'email.max' => 'メールアドレスは:max文字以内で入力してください。',
            'email.unique' => 'このメールアドレスは既に使用されています。',

            'password.required' => 'パスワードは必ず入力してください。',
            'password.string' => 'パスワードは文字列で入力してください。',
            'password.min' => 'パスワードは:min文字以上で入力してください。',
            'password.confirmed' => 'パスワードが確認用と一致しません。',

            'postal_code.required' => '郵便番号は必ず入力してください。',
            'postal_code.string' => '郵便番号は文字列で入力してください。',
            'postal_code.regex' => '郵便番号は正しい形式（例: 123-4567）で入力してください。',
            'postal_code.max' => '郵便番号は:max文字以内で入力してください。',

            'address.required' => '住所は必ず入力してください。',
            'address.string' => '住所は文字列で入力してください。',
            'address.max' => '住所は:max文字以内で入力してください。',

            'phone.required' => '電話番号は必ず入力してください。',
            'phone.string' => '電話番号は文字列で入力してください。',
            'phone.regex' => '電話番号は正しい形式（例: 090-0000-0000）で入力してください。',
            'phone.max' => '電話番号は:max文字以内で入力してください。',
        ];
    }
}
