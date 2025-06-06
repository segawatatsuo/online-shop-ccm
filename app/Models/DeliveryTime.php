<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryTime extends Model
{
    use HasFactory;
    protected $table = 'delivery_times'; // テーブル名が規約と異なる場合
    protected $fillable = ['time', 'shipping_company'];
}
