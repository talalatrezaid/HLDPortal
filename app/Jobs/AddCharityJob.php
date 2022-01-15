<?php

namespace App\Jobs;

use App\Mail\AddCharityEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class AddCharityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $send_mail;
    protected $charity;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($send_mail, $c)
    {
        $this->send_mail = $send_mail;
        $this->charity = $c;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new  AddCharityEmail($this->charity);
        Mail::to($this->send_mail)->send($email);
    }
}
