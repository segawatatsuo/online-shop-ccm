<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['sei', 'mei', 'email', 'phone', 'zip', 'input_add01', 'input_add02', 'input_add03'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the customer's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->sei . ' ' . $this->mei;
    }

    public function getFullAddressAttribute()
    {
        return $this->input_add01 . ' ' . $this->input_add02 . ' ' . $this->input_add03;
    }
}
