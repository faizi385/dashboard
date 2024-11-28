<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LpPendingStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lpName;
    public $lpEmail;

    /**
     * Create a new message instance.
     *
     * @param string $lpName
     * @param string $lpEmail
     * @return void
     */
    public function __construct($lpName, $lpEmail)
    {
        $this->lpName = $lpName;
        $this->lpEmail = $lpEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Supplier Registration Received')
                    ->view('emails.lp_pending_status')
                    ->with([
                        'lpName' => $this->lpName,
                        'lpEmail' => $this->lpEmail,
                    ]);
    }
}

