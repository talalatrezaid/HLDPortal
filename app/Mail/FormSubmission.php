<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormSubmission extends Mailable
{
    use Queueable, SerializesModels;


    public $user;

    public function __construct($user){

        $this->user = $user;

    }    

    public function build()
    {
      // Check If Files Included or not
      if(array_key_exists('file_name',$this->user))
      {
        // Get Individual File Name
        $files = explode(',',$this->user['file_name']);

        $email =    $this->from($this->user['sender'])
                    ->markdown('admin.mails.formSubmission')
                    ->subject($this->user['subject'])
                    ->with('user', $this->user);
        // Attach Each File In Mail
        foreach($files as $key => $value)
        {
          $email->attach(public_path().'/storage/images/forms/'.$this->user['form_id'].'/'.$value.'');
        }
        return $email;
      }
      else
      {
        // In Case No File Found
        return $this->from($this->user['sender'])
                    ->markdown('admin.mails.formSubmission')
                    ->subject($this->user['subject'])
                    ->with('user', $this->user);
      }
    }
}
