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


}
