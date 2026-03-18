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

    private function renderTemplate(string $template, array $context): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.-]+)\s*\}\}/', function ($m) use ($context) {
            $value = data_get($context, $m[1]);
            if (is_array($value) || is_object($value)) {
                return json_encode($value);
            }
            return (string) ($value ?? '');
        }, $template) ?? $template;
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
        try {
            $payload = json_decode(json_encode($payload), true) ?: [];

            $toTemplate = (string) ($payload['to'] ?? '');
            $messageTemplate = (string) ($payload['message'] ?? '');
            $otpLength = (int) ($payload['otp_length'] ?? 6);

            $otp = $this->generateOtp($otpLength);
            $context = array_merge($variables, [
                'payload' => $payload,
                'otp' => $otp,
            ]);

            $to = trim($this->renderTemplate($toTemplate, $context));
            $message = $this->renderTemplate($messageTemplate, $context);



            if ($to === '' || trim($message) === '') {

                Log::warning('SendOTP missing fields', ['to' => $to]);
                return false;
            }

            if (strpos($to, '@') !== false) {
                $mailable = new EmailTemplate(
                    email_subject: 'Your OTP Code',
                    email_body: $message
                );

                Mail::to($to)->send($mailable);

                $sent = true;
            } else {
                // Use existing OTP helper (sends OTP via provider)
                if (empty(env('INFINITO_CLIENT_ID')) || empty(env('INFINITO_CLIENT_PASSWORD')) || empty(env('INFINITO_SENDER_ID'))) {
                    Log::error('SendOTP missing INFINITO env vars', [
                        'has_client_id' => !empty(env('INFINITO_CLIENT_ID')),
                        'has_client_password' => !empty(env('INFINITO_CLIENT_PASSWORD')),
                        'has_sender_id' => !empty(env('INFINITO_SENDER_ID')),
                    ]);
                    return false;
                }

                $sent = send_client_otp($to, $otp);
            }



            Log::info('SendOTP result', ['to' => $to, 'sent' => $sent]);
            return (bool) $sent;
        } catch (\Throwable $e) {
            Log::error('SendOTP failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
