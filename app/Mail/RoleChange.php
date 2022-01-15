<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RoleChange extends Mailable
{
        use Queueable, SerializesModels;

    
    public $user;

    public function __construct($user){

        $this->user = $user;

    }    

   public function build()
    {

    return $this->markdown('admin.mails.rolechange')
                ->subject('Your account has been approved!')
                ->with('user', $this->user);
    }
}
