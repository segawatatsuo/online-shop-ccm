<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $order, $customer, $delivery;

    public function __construct($order, $customer, $delivery)
    {
        $this->order = $order;
        $this->customer = $customer;
        $this->delivery = $delivery;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('【CCメディコ ご注文ありがとうございました】' . $this->order->order_number )
                    ->markdown('emails.order.confirmed')
                    ->with([
                        'order'    => $this->order,
                        'customer' => $this->customer,
                        'delivery' => $this->delivery,
                    ]);
    }
}
