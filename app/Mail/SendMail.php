<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;



    public $details;



    /**

     * Create a new message instance.

     *

     * @return void

     */

    public function __construct($details)

    {

        $this->details = $details;

    }



    /**

     * Build the message.

     *

     * @return $this

     */

    public function build()

    {

        return $this->from('no-reply@emebet.net','Emebet')
                    ->subject('Your Credentials')
                    ->view('email/sendPassword');
    }
}
