<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    protected $password;
    protected $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($password, $email)
    {
        $this->password = $password;
		$this->mail = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('lms.mail.newuser')->with('password', $this->password)->with('email', $this->mail)
            ->from('contact@askmethod.com', 'Ryan Levesque')
            ->subject('IMPORTANT: Login Details ASK Method Masterclass! (Save this Email)');
    }
}
