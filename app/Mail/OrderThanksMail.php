<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class OrderThanksMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order; // ← ここが大事！

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->markdown('emails.order_thanks');
        return $this->subject('ご注文ありがとうございます')
        ->markdown('emails.order_thanks')
        ->with(['order' => $this->order]);
    }
}
