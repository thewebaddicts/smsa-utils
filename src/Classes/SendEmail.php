<?php

namespace twa\smsautils\Classes;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use twa\smsautils\Mail\EmailTemplate;

class SendEmail
{
    public function label(): string
    {
        return 'Send Email';
    }

    public function payload(): array
    {
        return [
            [
                'column' => 'to',
                'label' => 'To',
                'type' => 'textfield',
                'required' => true,
                'placeholder' => 'e.g. shipper.email or consignee.email',
            ],
            [
                'column' => 'subject',
                'label' => 'Subject',
                'type' => 'textfield',
                'required' => true,
                'placeholder' => 'Enter email subject',
            ],
            [
                'column' => 'message',
                'label' => 'Message',
                'type' => 'texteditor',
                'required' => true,
                'placeholder' => 'Enter email message',
            ],
       

        ];
    }

    private function renderTemplate(string $template, array $context): string
    {
        return str_replace(array_keys($context), array_values($context), $template);
    }




    public function handle(array $variables, string|null $payload): bool
    {

        if (!$payload) {
            Log::warning('SendEmail: Empty payload provided.');
            return false;
        }

        $dictionary = render_dictionary_template($variables);

        $payload = $this->renderTemplate($payload, $dictionary);

        try {
            $payload = json_decode($payload, true);
        } catch (\Throwable $th) {
            Log::error('SendEmail: Failed to decode payload JSON.', [
                'error' => $th->getMessage(),
            ]);
            return false;
        }

        if (!is_array($payload)) {
            Log::warning('SendEmail: Decoded payload is not an array.');
            return false;
        }

        try {
            $to = $payload['to'];
            $subject = $payload['subject'];
            $message = $payload['message'];
        } catch (\Throwable $th) {
            Log::error('SendEmail: Missing required payload keys.', [
                'error' => $th->getMessage(),
                'payload_keys' => array_keys($payload),
            ]);
            return false;
        }

        $emailShape = [
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
        ];


        try {
            Log::info('SendEmail: Attempting to send email.', [
                'email' => $emailShape,
            ]);

            Mail::to($to)->send(new EmailTemplate(
                email_subject: $subject,
                email_body: $message,
            ));

            Log::info('SendEmail: Email sent successfully.', [
                'email' => $emailShape,
            ]);
        } catch (\Throwable $th) {
            Log::error('SendEmail: Failed to send email.', [
                'email' => $emailShape ?? null,
                'error' => $th->getMessage(),
            ]);
            return false;
        }

        return true;
    }
}
