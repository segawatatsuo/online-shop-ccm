<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 
        'customer_id', 
        'delivery_id', 
        'user_id', 
        'total_price', 
        'shipping_date', 
        'tracking_number',
        'shipping_company',
        'delivery_date', 
        'delivery_time', 
        'your_request', 
        'status'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orderDetails()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }




    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * Get the created_at attribute as a Carbon instance in Asia/Tokyo timezone.
     *
     * @param  string  $value
     * @return \Carbon\Carbon
     */
    public function getCreatedAtAttribute($value)
    {
        // データベースから取得したUTCの値をCarbonでパースし、指定のタイムゾーンに変換
        return Carbon::parse($value)->timezone('Asia/Tokyo');
    }


        /**
     * 新しい注文番号を生成します。
     * 重複が発生した場合は自動的にリトライします。
     *
     * @return string
     * @throws \RuntimeException ユニークな注文番号の生成に失敗した場合
     */
    public static function generateOrderNumber(): string
    {
        $maxAttempts = 5; // 最大リトライ回数
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            $date = now()->format('Ymd');
            $latestOrder = self::whereDate('created_at', now()->toDateString())
                               ->latest('id')
                               ->first();

            $number = $latestOrder ? ((int)substr($latestOrder->order_number, -4)) + 1 : 1;
            $orderNumber = 'ORD' . $date . str_pad($number, 4, '0', STR_PAD_LEFT);

            // 生成された注文番号が既に存在しないか確認
            if (!self::where('order_number', $orderNumber)->exists()) {
                return $orderNumber; // 重複がなければこの番号を採用
            }

            $attempt++;
            usleep(100000); // 100ミリ秒待機 (マイクロ秒単位)
        }

        // 最大リトライ回数を超えてもユニークな番号が生成できなかった場合
        \Log::error('Failed to generate a unique order number after ' . $maxAttempts . ' attempts.');
        throw new \RuntimeException('Unable to generate a unique order number.');
    }


}
