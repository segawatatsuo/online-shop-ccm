<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 必ず true に（認可不要な場合）
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'お名前は必須です。',
            'email.required'   => 'メールアドレスは必須です。',
            'email.email'      => '有効なメールアドレスを入力してください。',
            'address.required' => '住所は必須です。',
        ];
    }
}
