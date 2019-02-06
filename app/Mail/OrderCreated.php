<?php

namespace CodeShopping\Mail;

use Illuminate\Mail\Mailable;

class OrderCreated extends Mailable
{
    public $order;

    /**
     * Terminal: php artisan make:mail OrderCreatedMail --markdown=mails.order_created
     *
     * @return void
     */
    public function __construct($order)
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
        return $this->markdown('mails.order_created');
    }
}

