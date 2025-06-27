<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopPage extends Model
{
    use HasFactory;
    protected $table = 'top_pages';
    protected $guarded = ['id'];
}
