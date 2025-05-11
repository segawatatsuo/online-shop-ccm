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
            'sei'    => ['required', 'string', 'max:30'],
            'mei'    => ['required', 'string', 'max:30'],
            'email'   => ['required', 'email'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'input_add01' => ['required', 'string', 'max:255'],
            'input_add02' => ['required', 'string', 'max:255'],
            'input_add03' => ['nullable', 'string', 'max:255'],

        ];
    }

    public function messages(): array
    {
        return [
            'sei.required'    => '姓は必須です。',
            'mei.required'    => '名は必須です。',
            'email.required'   => 'メールアドレスは必須です。',
            'email.email'      => '有効なメールアドレスを入力してください。',
            'input_add01.required' => '住所（都道府県）は必須です。',
            'input_add02.required' => '住所（市区町村）は必須です。',
        ];
    }
}
