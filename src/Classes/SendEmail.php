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
                'type' => 'textarea',
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
            return false;
        }

        $dictionary = render_dictionary_template($variables);

        $payload = $this->renderTemplate($payload, $dictionary);

        try {
            $payload = json_decode($payload, true);
        } catch (\Throwable $th) {
            return false;
        }

        if (!is_array($payload)) {
            return false;
        }

        try {
            $to = $payload['to'];
            $subject = $payload['subject'];
            $message = $payload['message'];
        } catch (\Throwable $th) {
            return false;
        }


        try {
            Mail::to($to)->send(new EmailTemplate(
                email_subject: $subject,
                email_body: $message,
            ));
        } catch (\Throwable $th) {
            return false;
        }

        return true;
    }
}
