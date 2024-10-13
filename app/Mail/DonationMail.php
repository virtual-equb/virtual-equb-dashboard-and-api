<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class DonationMail extends Mailable
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

        return $this->from('no-reply@emebet.net', 'Emebet')
            ->subject('Donation Details')
            ->view('email/donationMail');
    }
}
