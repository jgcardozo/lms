<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    protected $uuid;
    protected $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($uuid, $email)
    {
        $this->uuid = $uuid;
		$this->mail = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('lms.mail.newuser')->with('uuid', $this->uuid)->with('email', $this->mail)
            ->from('contact@askmethod.com', 'Ryan Levesque')
            ->subject('IMPORTANT: Login Details ASK Method Masterclass! (Save this Email)');
    }
}
