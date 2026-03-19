<?php

namespace twa\smsautils\Classes;

use twa\smsautils\Mail\EmailTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOTP
{
    public function label(): string
    {
        return 'Send OTP';
    }

    public function payload(): array
    {
        return [
            [
                'column' => 'to',
                'label' => 'To',
                'type' => 'textfield',
                'required' => true,
                'placeholder' => 'e.g. {{shipper.phone}} or {{consignee.phone}}',
            ],
            [
                'column' => 'message',
                'label' => 'Message',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'e.g. Your OTP is {{otp}}',
            ],
            [
                'column' => 'otp_length',
                'label' => 'OTP Length',
                'type' => 'textfield',
                'required' => false,
                'placeholder' => 'default 6',
            ],

        ];
    }

   
    private function generateOtp(int $length = 6): string
    {
        $length = max(4, min(10, $length));
        $min = (int) str_pad('1', $length, '0');
        $max = (int) str_pad('', $length, '9');

        return (string) random_int($min, $max);
    }

    public function handle(array $variables, string|null $payload): bool
    {
        if (!$payload) return false;
    
        $otp = $this->generateOtp(6);
    
        $dictionary = render_dictionary_template($variables);
        $dictionary['{{otp}}'] = $otp;
    
        $payload = str_replace(array_keys($dictionary), array_values($dictionary), $payload);
    
        $payload = json_decode($payload, true);
        if (!is_array($payload)) return false;
    
        $to = $payload['to'] ?? null;
        $message = $payload['message'] ?? null;
        if (!$to || !$message) return false;
    
        if (strpos($to, '@') !== false) {
            Mail::to($to)->send(new EmailTemplate(
                email_subject: 'Your OTP Code',
                email_body: $message
            ));
            return true;
        }
    
        return send_client_otp($to, $otp);
    }
}
