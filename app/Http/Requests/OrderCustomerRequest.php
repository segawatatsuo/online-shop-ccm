<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 必ず true に（認可不要な場合）
    }


    public function prepareForValidation()
    {
        if ($this->input('same_as_orderer') == '1') {
            $this->merge([
                'delivery_sei' => $this->input('order_sei'),
                'delivery_mei' => $this->input('order_mei'),
                'delivery_zip' => $this->input('order_zip'),
                'delivery_email' => $this->input('order_email'),
                'delivery_phone' => $this->input('order_phone'),
                'delivery_add01' => $this->input('order_add01'),
                'delivery_add02' => $this->input('order_add02'),
                'delivery_add03' => $this->input('order_add03'),
            ]);
        }
    }


    public function rules(): array
    {
        return [
            'order_sei'    => ['required', 'string', 'max:30'],
            'order_mei'    => ['required', 'string', 'max:30'],
            'order_zip'    => ['required', 'string', 'max:8'],
            'order_email'   => ['required', 'email'],
            'order_phone'   => ['nullable', 'string', 'max:20'],
            'order_add01' => ['required', 'string', 'max:255'],
            'order_add02' => ['required', 'string', 'max:255'],
            'order_add03' => ['nullable', 'string', 'max:255'],
            'delivery_date' => ['nullable'],
            'delivery_time' => ['nullable'],
            'your_request' => ['nullable'],

            'same_as_orderer' => ['nullable'],

            'delivery_sei'    => 'required_if:same_as_orderer,0|nullable|string|max:30',
            'delivery_mei'    => 'required_if:same_as_orderer,0|nullable|string|max:30',
            'delivery_zip'    => 'required_if:same_as_orderer,0|nullable|string|max:8',
            'delivery_email'   => 'required_if:same_as_orderer,0|nullable|email',
            'delivery_phone'   => 'required_if:same_as_orderer,0|nullable|string|max:20',
            'delivery_add01' => 'required_if:same_as_orderer,0|nullable|string|max:255',
            'delivery_add02' => 'required_if:same_as_orderer,0|nullable|string|max:255',
            'delivery_add03' => ['nullable', 'string', 'max:255'],


        ];
    }

    public function messages(): array
    {
        return [
            'order_sei.required'    => '姓は必須です。',
            'order_mei.required'    => '名は必須です。',
            'order_zip.required'    => '郵便番号は必須です。',
            'order_email.required'   => 'メールアドレスは必須です。',
            'order_email.email'      => '有効なメールアドレスを入力してください。',
            'order_add01.required' => '住所（都道府県）は必須です。',
            'order_add02.required' => '住所（市区町村）は必須です。',


            // delivery_sei
            'delivery_sei.required_if' => 'お届け先の姓は必須です。',
            'delivery_sei.string' => 'お届け先の姓は文字列で入力してください。',
            'delivery_sei.max' => 'お届け先の姓は :max 文字以内で入力してください。',

            // delivery_mei
            'delivery_mei.required_if' => 'お届け先の名は必須です。',
            'delivery_mei.string'      => 'お届け先の名は文字列で入力してください。',
            'delivery_mei.max'         => 'お届け先の名は :max 文字以内で入力してください。',

            // delivery_email
            'delivery_email.required_if' => 'お届け先のメールアドレスは必須です。',
            'delivery_email.email'       => 'お届け先のメールアドレスの形式が正しくありません。',
            'delivery_email.max'         => 'お届け先のメールアドレスは :max 文字以内で入力してください。',

            // delivery_zip
            'delivery_zip.required_if' => 'お届け先の郵便番号は必須です。',
            'delivery_zip.string'      => 'お届け先の郵便番号は文字列で入力してください。',
            'delivery_zip.max'         => 'お届け先の郵便番号は :max 文字以内で入力してください。',

            // delivery_phone
            'delivery_phone.required_if' => 'お届け先の電話番号は必須です。',
            'delivery_phone.string'      => 'お届け先の電話番号は文字列で入力してください。',
            'delivery_phone.max'         => 'お届け先の電話番号は :max 文字以内で入力してください。',

            // delivery_add01
            'delivery_add01.required_if' => 'お届け先の都道府県は必須です。',
            'delivery_add01.string'      => 'お届け先の都道府県は文字列で入力してください。',
            'delivery_add01.max'         => 'お届け先の都道府県は :max 文字以内で入力してください。',

            // delivery_add02
            'delivery_add02.required_if' => 'お届け先の市区町村は必須です。',
            'delivery_add02.string'      => 'お届け先の市区町村は文字列で入力してください。',
            'delivery_add02.max'         => 'お届け先の市区町村は :max 文字以内で入力してください。',

            // delivery_add03（任意入力の場合）
            'delivery_add03.string' => 'お届け先の番地・建物名は文字列で入力してください。',
            'delivery_add03.max'    => 'お届け先の番地・建物名は :max 文字以内で入力してください。',
        ];
    }
}
