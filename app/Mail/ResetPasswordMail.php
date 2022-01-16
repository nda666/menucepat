<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $newPassword;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($newPassword, User $user)
    {
        $this->newPassword = $newPassword;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->user;
        $this->withSwiftMessage(function ($message) use ($user) {
            $message->user = $user;
        });
        $this->subject('Reset Password');
        return $this->view('emails.reset');
    }
}
