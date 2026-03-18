<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Log;

class SendWP
{
    public function label(): string
    {
        return 'Send WP';
    }

    public function payload(): array
    {
        return [

            [
                'column' => 'message',
                'label' => 'Message',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Enter notification message',
            ],

        ];
    }
    public function handle(array $variables, string|null $payload): bool
    {
        Log::info('SendWP', ['variables' => $variables, 'payload' => $payload]);
        return true;
    }
}
