<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use twa\smsautils\Models\AccessToken;

class CreateClientPortalNotification extends HandlerParent
{
    public function label(): string
    {
        return 'Create Client Portal Notification';
    }

    public function payload(): array
    {

        return [
            [
                'column' => 'awb',
                'label' => 'AWB',
                'type' => 'textfield',
                'required' => true,
                'placeholder' => 'Enter AWB',
            ],
        ];
    }
    public function handle(array $variables, string|null $payload): bool
    {
        $client_portal_notification = new ClientPortalNotification();
        $client_portal_notification->title = $variables['title'];
        $client_portal_notification->message = $variables['message'];
        $client_portal_notification->type = $variables['type'];
        $client_portal_notification->data = $variables['data'];
        $client_portal_notification->save();

        return true;
    }
}
