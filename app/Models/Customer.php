<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['sei', 'mei', 'email', 'phone', 'input_add01', 'input_add02', 'input_add03'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

