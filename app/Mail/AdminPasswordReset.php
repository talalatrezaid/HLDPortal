<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    
    public $user;

    public function __construct($user){

        $this->user = $user;

    }    

   public function build()
    {

    return $this->markdown('admin.mails.adminpassword')
                ->subject('Password has been Reset')    
                ->with('user', $this->user);
    }
}
