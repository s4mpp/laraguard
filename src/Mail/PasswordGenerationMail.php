<?php

namespace S4mpp\Laraguard\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Auth\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordGenerationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;

    public $link;

    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $link, string $password)
    {
        $this->user = $user;

        $this->link = $link;
        
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('laraguard::mail.password_generation');
    }
}
