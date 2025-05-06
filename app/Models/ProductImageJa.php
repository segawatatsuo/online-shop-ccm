<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImageJa extends Model
{
    use HasFactory;

    protected $fillable = ['product_ja_id', 'filename', 'is_main'];

    public function product(): BelongsTo
    {
      return $this->belongsTo(ProductJa::class);
    }
}
