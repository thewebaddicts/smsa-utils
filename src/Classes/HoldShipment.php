<?php

namespace twa\smsautils\Classes;

use App\Contracts\EventHandlerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use twa\smsautils\Models\AccessToken;
use twa\smsautils\Models\ClientPortalNotification;

class HoldShipment extends HandlerParent
{
    public function label(): string
    {
        return 'Hold Shipment';
    }

    public function payload(): array
    {

        return [


            [
                'column' => 'reason',
                'label' => 'Reason',
                'type' => 'textarea',
                'required' => true,
                'placeholder' => 'Enter Reason',
            ],
            [
                'column' => 'nb_hold_days',
                'label' => 'Number of Hold Days',
                'type' => 'textfield',
                'required' => true,
                'placeholder' => 'Enter Number of Hold Days',
            ],
        ];
    }
    public function handle(array $variables, string|null $payload): bool
    {

        $payload = $this->validatePayload($variables, $payload);
        if (!$payload) {
            return false;
        }

        DB::table('on_hold_awbs')->insert([
            'awb' => $variables['awb'],
            'shipment_id' => (string) $variables['shipment_id'],
            'awb_sequence' => $variables['awb_sequence'],
            'nb_packages' => $variables['nb_packages'],
            'master_awb' => $variables['parent_awb'],
            'reason' => (string) $payload['reason'],
            'hold_starts_at' => now(),
            'hold_expires_at' => now()->addDays((int) $payload['nb_hold_days']),
            'additional_notes' => null,
            'shelf_id' =>  null,
            'on_hold' => true,
        ]);

        return true;
    }
}
