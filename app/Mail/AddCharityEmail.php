<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AddCharityEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $charity;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($c)
    {
        $this->charity =  $c;

        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Welcome To Charities")->markdown('admin.mails.addcharity', ["user" => $this->charity]);
    }
}
