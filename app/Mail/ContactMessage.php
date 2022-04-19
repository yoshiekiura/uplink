<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $admin;
    public $data;

    public function __construct($props)
    {
        $this->admin = $props['admin'];
        $this->data = $props['data'];
    }

    /**
     * Build the data.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Uplink Contact Form')->view('emails.ContactMessage', [
            'admin' => $this->admin,
            'data' => $this->data,
        ]);
    }
}
