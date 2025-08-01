<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderNotification extends Mailable
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
        return $this->subject('【ご注文確認】' . $this->order->order_number . ' のご注文を受け付けました')
                    ->markdown('emails.order.confirmed')
                    ->with([
                        'order'    => $this->order,
                        'customer' => $this->customer,
                        'delivery' => $this->delivery,
                    ]);
    }
}
