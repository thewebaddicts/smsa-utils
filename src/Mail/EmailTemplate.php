<?php

namespace twa\smsautils\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailTemplate extends Mailable
{
    use Queueable, SerializesModels;

    public string $email_subject;
    public string $email_body;

    public function __construct(
        string $email_subject,
        string $email_body
    ) {

        $this->email_subject = $email_subject;
        $this->email_body = $email_body;
    }

    public function build(): self
    {
        return $this->subject($this->email_subject)
            ->view('emails.email-template');
    }
}
