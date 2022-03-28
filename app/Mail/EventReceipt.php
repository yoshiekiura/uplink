<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventReceipt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $user;
    public $event;
    public $visitor;

    public function __construct($props)
    {
        $this->user = $props['user'];
        $this->event = $props['event'];
        $this->visitor = $props['visitor'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Receipt untuk Event')->view('emails.EventReceipt', [
            'event' => $this->event,
            'user' => $this->user,
            'visitor' => $this->visitor,
        ]);
    }
}
