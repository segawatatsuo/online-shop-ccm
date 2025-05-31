<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductJa extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description', 'price', 'member_price', 'stock', 'image', 'sort_order'];

    protected $table = 'product_jas'; // ← これを忘れずに！Laravelにproduct_jasテーブルを認識させる

    public function category()
    {
        //Category::classは関連するモデルである App\Models\Category クラスへの完全修飾名
        return $this->belongsTo(Category::class);
    }
    /*「1対多」 のリレーションシップを定義*/
    /* hasMany リレーションシップの場合は、関連するモデルの複数形（OrderItem）をメソッド名に用います。*/
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function getPriceForUser($user)
    {
        if ($user) {
            return $this->member_price ?? $this->price;
        }
        return $this->price;
    }
    public function images(): HasMany
    {
        return $this->hasMany(ProductImageJa::class);
    }
    // メイン画像だけを取得するリレーション
    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImageJa::class)->where('is_main', 1);
    }

    public function subImages(): HasMany
    {
        return $this->hasMany(ProductImageJa::class)->where('is_sub', 1);
    }
}
