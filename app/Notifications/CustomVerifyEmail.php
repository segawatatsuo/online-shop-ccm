<?php

// app/Notifications/CustomVerifyEmail.php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmail
{
    /**
     * メール通知の内容を定義
     */
    public function toMail($notifiable)
    {
        $customer = $notifiable->corporateCustomer;
        // データが存在するか確認（null対策）
        $company = $customer->order_company_name ?? '';
        $department = $customer->order_department ?? '';
        $name = trim(($customer->order_sei ?? '') . ' ' . ($customer->order_mei ?? ''));

        // 署名付き確認URLを生成
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('CCメディコ会員登録確認メール')
            ->greeting("{$company} {$department} {$name} 様")
            ->line('CCメディコ法人会員ご登録ありがとうございます。以下のボタンをクリックして、登録を完了してください。')
            ->action('会員登録', $verificationUrl)
            ->line('このリンクは ' . config('auth.verification.expire', 60) . ' 分間有効です。')
            ->line('ご不明な点がありましたら、お気軽にお問い合わせください。');
    }

    /**
     * 署名付きURLの生成ロジック（任意でカスタマイズ可能）
     */
    protected function verificationUrl($notifiable)
    {
        // ユーザーのuser_typeに応じて異なるルートを使用
        if ($notifiable->user_type === 'corporate') {
            return URL::temporarySignedRoute(
                'corporate.verification.verify', // 新しく定義した法人用ルート名
                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        } else {
            // 個人ユーザーの場合は既存のルートを使用
            return URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        }
    }
}