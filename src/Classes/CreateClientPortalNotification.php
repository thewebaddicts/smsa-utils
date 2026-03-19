<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use twa\smsautils\Models\AccessToken;
use twa\smsautils\Models\ClientPortalNotification;

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
                'column' => 'title',
                'label' => 'Title',
                'type' => 'textfield',
                'required' => true,
                'placeholder' => 'Enter Title',
            ],
            [
                'column' => 'message',
                'label' => 'Message',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Enter Message',
            ],
        ];
    }
    public function handle(array $variables, string|null $payload): bool
    {

        $payload = $this->validatePayload($variables, $payload);
        if (!$payload) {
            return false;
        }

        $client_portal_notifications = new ClientPortalNotification();
        $client_portal_notifications->shipper_id = $variables['shipper']['id'];
        $client_portal_notifications->title = $payload['title'];
        $client_portal_notifications->message = $payload['message'];
        $client_portal_notifications->save();



        return true;
    }
}
