<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    use HasFactory;

    protected $table = 'mail_logs';

    protected $fillable = [
        'order_id',
        'mail_type',
        'recipient_email',
        'subject',
        'body',
        'status',
        'error_message',
        'sent_at',
    ];
}
