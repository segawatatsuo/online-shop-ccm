<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\Relations\HasOne; 

class Product extends Model
{
    /********
    belongsTo リレーションシップの場合は、関連するモデルの単数形（Category）をメソッド名に用います。
    $this->belongsTo(Category::class); は、Product モデルが Category モデルに属するという
    「多対1」のリレーションシップを定義し、関連するデータをEloquentを通じて簡単に操作できるようにするための記述です。
    このリレーションシップが正しく機能するためには、以下の前提条件が必要です。
    1.Category モデルが存在すること: app/Models/Category.php ファイルに Category モデルが定義されている必要があります。
    2.products テーブルに外部キーカラム category_id が存在すること: products データベーステーブルに、
    関連する categories テーブルの主キー（通常は id）を格納するための category_id という名前のカラムが存在する必要があります。
    3.category_id カラムが categories テーブルの id カラムを参照する外部キー制約が設定されていることが推奨されます
    （必須ではありませんが、データの整合性を保つ上で重要です）。
    *********/
    
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description', 'price', 'member_price', 'stock', 'image'];
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
        if($user){
            return $this->member_price ?? $this->price;
        }
        return$this->price;
    }
    public function images(): HasMany 
    { 
        return $this->hasMany(ProductImage::class); 
    } 
    // メイン画像だけを取得するリレーション
    public function mainImage(): HasOne 
    { 
        return $this->hasOne(ProductImage::class)->where('is_main', true); 
    }
}