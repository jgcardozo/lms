<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExistingUser extends Mailable
{
    use Queueable, SerializesModels;

    protected $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->mail = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('lms.mail.existinguser')->with('email', $this->mail)
            ->from('contact@askmethodmail.com', 'Ryan Levesque')
            ->subject('IMPORTANT: Login Details ASK Method Training Portal! (Save this Email)');
    }
}
