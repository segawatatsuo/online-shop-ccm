<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $table = 'deliveries';
    protected $fillable = ['sei', 'mei', 'email', 'phone', 'zip', 'input_add01', 'input_add02', 'input_add03'];
}
