<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Log;

class SendSMS extends HandlerParent
{
    public function label(): string
    {
        return 'Send SMS';
    }

    public function payload(): array
    {
        return [
            [
                'column' => 'to',
                'label' => 'To',
                'type' => 'textfield',
                'required' => true,
                'placeholder' => 'e.g. shipper.phone or consignee.phone',
            ],
            [
                'column' => 'message',
                'label' => 'Message',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Enter SMS message from approved list',
            ],

        ];
    }
    public function handle(array $variables, string|null $payload): bool
    {

        $payload = $this->validatePayload($variables, $payload);
        if (!$payload) {
            return false;
        }

        $clientId = env('INFINITO_CLIENT_ID');
        $clientPassword = env('INFINITO_CLIENT_PASSWORD');
        $senderId = env('INFINITO_SENDER_ID');
        $message = $payload['message'];
        $phone = $payload['to'];

        $query = http_build_query([
            'clientid' => $clientId,
            'clientpassword' => $clientPassword,
            'from' => $senderId,
            'to' => $phone,
            'text' => $message
        ]);

        $url = "https://api.goinfinito.me/unified/v2/send?" . $query;

        try {

            $response = file_get_contents($url);
            parse_str($response, $result);

            if (isset($result['statuscode']) && $result['statuscode'] === '0') {
                return true;
            }

            return false;
        } catch (\Exception $e) {

            return false;
        }

        return true;
    }
}
