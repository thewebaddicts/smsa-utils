<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Log;

class SendWebhook
{
    public function label(): string
    {
        return 'Send Webhook';
    }

    public function payload(): array
    {
        return [
            [
                'column' => 'label',
                'label' => 'Label',
                'type' => 'textfield',
                'required' => true,
                'placeholder' => 'Enter label',
            ],

        ];
    }
    public function handle(array $variables, string|null $payload): bool
    {
        Log::info('SendWebhook', ['variables' => $variables, 'payload' => $payload]);
        return true;
    }
}
