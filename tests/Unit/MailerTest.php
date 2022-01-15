<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Mail\MailTesting;
use Illuminate\Support\Facades\Mail;

class MailerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
    public function testSending()
   {
       Mail::fake();

       Mail::send(new MailTesting());

       Mail::assertSent(MailTesting::class);

       Mail::assertSent(MailTesting::class, function ($mail) {
           $mail->build();
           $this->assertTrue($mail->hasFrom('hello@mailtrap.io'));
           $this->assertTrue($mail->hasCc('hola@mailtrap.io'));

           return true;
       });
   }
}
