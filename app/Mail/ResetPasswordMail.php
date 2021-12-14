<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\Helpers\Builder\Variable;
use MailerSend\LaravelDriver\MailerSendTrait;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels, MailerSendTrait;

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
        ])->mailersend(
            // Template ID
            null,
            // Variables for simple personalization
            [
                new Variable($to, ['name' => 'Your Name'])
            ],
            // Tags
            ['tag'],
            // Advanced personalization
            [
                new Personalization($to, [
                    'var' => 'variable',
                    'number' => 123,
                    'object' => [
                        'key' => 'object-value'
                    ],
                    'objectCollection' => [
                        [
                            'name' => 'John'
                        ],
                        [
                            'name' => 'Patrick'
                        ]
                    ],
                ])
            ]
        );
    }
}
