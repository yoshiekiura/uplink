<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentComplete extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public $cart;
    public $classToCall;
    public function __construct($props)
    {
        $this->cart = $props['cart'];
        $this->classToCall = $props['classToCall'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.PaymentComplete', [
            'cart' => $this->cart,
            'classToCall' => $this->classToCall,
        ]);
    }
}
