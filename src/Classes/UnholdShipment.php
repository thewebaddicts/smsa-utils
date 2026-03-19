<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use twa\smsautils\Models\AccessToken;
use twa\smsautils\Models\ClientPortalNotification;

class   UnholdShipment extends HandlerParent
{
    public function label(): string
    {
        return 'UnHold Shipment';
    }

    public function payload(): array
    {

        return [
     
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
