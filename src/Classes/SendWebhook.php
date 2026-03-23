<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWebhook extends HandlerParent
{
    public function label(): string
    {
        return 'Send Webhook';
    }

    public function payload(): array
    {
        return [
            [
                'column' => 'url',
                'label' => 'Webhook POST URL',
                'type' => 'textfield',
                'required' => true,
                'placeholder' => 'Enter Webhook POST URL',
            ],

        ];
    }

    public function handle(array $variables, string|null $payload): bool
    {
        $payload = $this->validatePayload($variables, $payload);
        if (!$payload) {
            return false;
        }


        

        $url = $payload['url'];

        $response = Http::post($url);
        if ($response->failed()) {
            return false;
        }

        return true;
    }
}
