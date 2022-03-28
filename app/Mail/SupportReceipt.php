<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $user;
    public $visitor;
    public $support;

    public function __construct($props)
    {
        $this->user = $props['user'];
        $this->visitor = $props['visitor'];
        $this->support = $props['support'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Receipt untuk Support')->view('emails.SupportReceipt', [
            'user' => $this->user,
            'visitor' => $this->visitor,
            'support' => $this->support,
        ]);
    }
}
