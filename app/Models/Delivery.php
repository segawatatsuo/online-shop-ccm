<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $table = 'deliveries';
    protected $fillable = ['sei', 'mei', 'email', 'phone', 'zip', 'input_add01', 'input_add02', 'input_add03'];

    public function getFullNameAttribute()
    {
        return $this->sei . ' ' . $this->mei;
    }

    public function getFullAddressAttribute()
    {
        return $this->input_add01 . ' ' . $this->input_add02 . ' ' . $this->input_add03;
    }
}
