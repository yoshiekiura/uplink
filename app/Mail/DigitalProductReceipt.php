<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DigitalProductReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public $user;
    public $visitor;
    public $product;

    public function __construct($props)
    {
        $this->user = $props['user'];
        $this->product = $props['product'];
        $this->visitor = $props['visitor'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Receipt untuk Digital Product')->view('emails.DigitalProductReceipt', [
            'user' => $this->user,
            'visitor' => $this->visitor,
            'product' => $this->product,
        ]);
    }
}
