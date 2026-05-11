<?php

namespace twa\smsautils\Enums;

enum PickupStatusEnum: string
{

    case PICKUP_CREATED = 'PKCR';
    case PICKUP_PICKED_UP = 'PKPU';
    case PICKUP_DELAYED = 'PKDL';
    case PICKUP_FAILED = 'PKFL';
    case PICKUP_CANCELLED = 'PKCN';


    case NOT_READY_TO_PICKUP = 'PKNR';
    case SHIPPER_HAS_ONLY_THESE = 'PKSH';

    // case MOBILE_CLOSED = 'PKMC';
    // case NO_ANSWER = 'PKNA';
    // case RESCHEDULE = 'PKRE';
    // case ANOTHER_VEHICLE_TYPE = 'PKAV';    
    // case VEHCILE_OVERLOADED = 'PKOV';
    // case CITY_CHANGED = 'PKCC';
    // case AREA_CHANGED = 'PKAC';
    // case ROUTE_CHANGED = 'PKRC';
    // case WRONG_ASSIGNMENT = 'PKWA';
    // case DUPLICATE_PICKUP = 'PKDP';
    // case DROPPED_AT_RETAILR = 'PKDR';
    // case OUT_OF_PICKUP_AREA = 'PKOA';


   
    public function info(): array
    {
        return match ($this) {

            self::PICKUP_CREATED => [
                'label' => 'Pending',
                "icon" => "calendar",
                "color_bg" => "#FFF4E6", // soft orange background
                "color_text" => "#D97706", // amber-700
                'description' => 'Pickup Created',
                'tags' => ["all"],
            ],
            self::PICKUP_CANCELLED => [
                'label' => 'Cancelled',
                "icon" => "x",
                "color_bg" => "#FEE2E2", // light red background
                "color_text" => "#B91C1C", // red-700
                'description' => 'Cancelled',
                'tags' => ["all"],
            ],
            self::PICKUP_FAILED => [
                'label' => 'Failed',
                "icon" => "alert-triangle",
                "color_bg" => "#FEF2F2", // very light red background
                "color_text" => "#DC2626", // red-600
                'description' => 'Failed',
                'tags' => ["all"],
            ],
            self::PICKUP_DELAYED => [
                'label' => 'Delayed',
                "icon" => "clock",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Delayed',
                'tags' => ["all"],
            ],
            self::PICKUP_PICKED_UP => [
                'label' => 'Picked Up',
                "icon" => "truck",
                "color_bg" => "#E0F2FE", // light blue background
                "color_text" => "#0369A1", // sky-800
                'description' => 'Picked Up',
                'tags' => ["all"],
            ],
            self::NOT_READY_TO_PICKUP => [
                'label' => 'Not Ready to Pickup',
                'icon' => 'map',
                'color_bg' => '#FEF9C3',
                'color_text' => '#92400E',
                'description' => 'Not Ready to Pickup',
                'tags' => ['MISSING_SCAN_REASON'],
            ],
            self::SHIPPER_HAS_ONLY_THESE => [
                'label' => 'Shipper has only these shipments',
                'icon' => 'map',
                'color_bg' => '#FEF9C3',
                'color_text' => '#92400E',
                'description' => 'Shipper has only these shipments',
                'tags' => ['MISSING_SCAN_REASON'],
            ],
        };
    }

    public function values(): array
    {
        return [
           
        ];
    }

    public function apiOptions(): array
    {
        $cases = $this->cases();

        return array_map(function ($case) {
            return [
                'value' => $case->value,
                'label' => $case->info()['label'],
                'icon' => $case->info()['icon'],
                'color_bg' => $case->info()['color_bg'],
                'color_text' => $case->info()['color_text'],
            ];
        }, $cases);
    }
}
