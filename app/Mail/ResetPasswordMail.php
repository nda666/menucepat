<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Webup\LaravelSendinBlue\SendinBlue;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels, SendinBlue;

    protected $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $to = Arr::get($this->to, '0.address');

        return $this->markdown('emails.reset', [
            'token' => $this->token
        ])->sendinblue([null]);
    }
}
