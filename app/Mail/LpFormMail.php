<?php


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LpFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lp;

    public function __construct($lp)
    {
        $this->lp = $lp;
    }

    public function build()
    {
        $link = route('lp.completeForm', $this->lp->id); // Generates a link to the form
        
        return $this->view('emails.lp_form')
        ->subject('LP Information Form')
            ->with([
                'name' => $this->lp->name,
                'link' => $link,
            ]);
    }
}
