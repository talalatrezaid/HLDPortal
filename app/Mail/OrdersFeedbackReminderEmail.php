<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrdersFeedbackReminderEmail extends Mailable
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
        //
        $this->charity =  $c;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("order complete email")->markdown('admin.mails.ordercompletereminderemail', ["user" => $this->charity]);
    }
}
