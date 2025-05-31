<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImageJa extends Model
{
    use HasFactory;

    protected $fillable = ['product_ja_id', 'image_path', 'order' ,'is_main','is_sub'];

    public function product(): BelongsTo
    {
      return $this->belongsTo(ProductJa::class);
    }
}
