<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewSellerNotification extends Mailable
{
        use Queueable, SerializesModels;

    
    public $user;

    public function __construct($user){
        $this->user = $user;

    }    

   public function build()
    {

    return $this->markdown('admin.mails.newsellernotification')
                ->subject('New Seller Notification!')
                ->with('user', $this->user);
    }
}
