<?php



namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LPStatusChangeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lp; // Pass the LP data
    public $status; // Pass the status (approved or rejected)

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($lp, $status)
    {
        $this->lp = $lp;
        $this->status = $status;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('LP Status Update')
                    ->view('emails.lp_status_change')
                    ->with([
                        'lp' => $this->lp,
                        'status' => $this->status,
                    ]);
    }
}
