<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\CustomVerifyEmail; // これがあることを確認
use Illuminate\Support\Facades\Log; // デバッグログを入れた場合はこれも必要

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'phone',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed', // もしLaravel 9以降なら通常は自動でhashedになります
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function corporateCustomer()
    {
        return $this->hasOne(CorporateCustomer::class);
    }

    /**
     * メール確認通知を送信します。
     * 法人ユーザーの場合はCustomVerifyEmailを使用
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        // Debugging logs from previous suggestions
        // Log::info('### User sendEmailVerificationNotification START ###');
        // Log::info('User ID: ' . $this->id);
        // Log::info('User Type (in User Model): ' . $this->user_type);

        if ($this->user_type === 'corporate') {
            // Log::info('User is corporate. Notifying with CustomVerifyEmail.');
            $this->notify(new CustomVerifyEmail);
        } else {
            // Log::info('User is individual or other. Notifying with CustomVerifyEmail.');
            $this->notify(new CustomVerifyEmail); // または parent::sendEmailVerificationNotification();
        }
        // Log::info('### User sendEmailVerificationNotification END ###');
    }
}