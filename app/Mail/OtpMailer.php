<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMailer extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user = null;
    public $otp = null;

    public function __construct()
    {
        $this->user = $props['user'];
        $this->otp = $props['otp'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('One Time Password - Uplink.id')->view('emails.otp', [
            'user' => $this->user,
            'otp' => $this->otp,
        ]);
    }
}
