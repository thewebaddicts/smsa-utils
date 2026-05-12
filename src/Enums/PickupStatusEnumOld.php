<?php

namespace twa\smsautils\Enums;

enum PickupStatusEnumOld: string
{

    case PICKUP_CREATED = 'PKCR';
    case PICKUP_PICKED_UP = 'PKPU';
    case PICKUP_DELAYED = 'PKDL';
    case PICKUP_FAILED = 'PKFL';
    case PICKUP_CANCELLED = 'PKCN';


    case NOT_READY_TO_PICKUP = 'PKNR';
    case SHIPPER_HAS_ONLY_THESE = 'PKSH';

    case MOBILE_CLOSED = 'PKMC';
    case NO_ANSWER = 'PKNA';
    case RESCHEDULE = 'PKRE';
    case ANOTHER_VEHICLE_TYPE = 'PKAV';
    case VEHCILE_OVERLOADED = 'PKOV';
    case CITY_CHANGED = 'PKCC';
    case AREA_CHANGED = 'PKAC';
    case ROUTE_CHANGED = 'PKRC';
    case WRONG_ASSIGNMENT = 'PKWA';
    case DUPLICATE_PICKUP = 'PKDP';
    case DROPPED_AT_RETAILR = 'PKDR';
    case OUT_OF_PICKUP_AREA = 'PKOA';



    public function info(): array
    {
        return match ($this) {

            self::PICKUP_CREATED => [
                'label' => 'Pending',
                "icon" => "calendar",
                "color_bg" => "#FFF4E6", // soft orange background
                "color_text" => "#D97706", // amber-700
                'description' => 'Pickup Created',
                'tags' => [],
                "type" => null,
            ],
            self::PICKUP_CANCELLED => [
                'label' => 'Cancelled',
                "icon" => "x",
                "color_bg" => "#FEE2E2", // light red background
                "color_text" => "#B91C1C", // red-700
                'description' => 'Cancelled',
                'tags' => [],
                "type" => null,
            ],
            self::PICKUP_FAILED => [
                'label' => 'Failed',
                "icon" => "alert-triangle",
                "color_bg" => "#FEF2F2", // very light red background
                "color_text" => "#DC2626", // red-600
                'description' => 'Failed',
                'tags' => [],
                "type" => null,
            ],
            self::PICKUP_DELAYED => [
                'label' => 'Delayed',
                "icon" => "clock",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Delayed',
                'tags' => [],
                "type" => null,
            ],
            self::PICKUP_PICKED_UP => [
                'label' => 'Picked Up',
                "icon" => "truck",
                "color_bg" => "#E0F2FE", // light blue background
                "color_text" => "#0369A1", // sky-800
                'description' => 'Picked Up',
                'tags' => [],
                "type" => null,
            ],



            self::NOT_READY_TO_PICKUP => [
                'label' => 'Not Ready to Pickup',
                'icon' => 'map',
                'color_bg' => '#FEF9C3',
                'color_text' => '#92400E',
                'description' => 'Not Ready to Pickup',
                'tags' => ['PICKUP_EXCEPTION'],
                "type" => 'EXCEPTIONS',
            ],
            self::SHIPPER_HAS_ONLY_THESE => [
                'label' => 'Shipper has only these shipments',
                'icon' => 'map',
                'color_bg' => '#FEF9C3',
                'color_text' => '#92400E',
                'description' => 'Shipper has only these shipments',
                'tags' => ['PICKUP_EXCEPTION'],
                "type" => 'EXCEPTIONS',
            ],

            self::MOBILE_CLOSED => [
                'label' => 'Mobile Closed',
                "icon" => "mobile",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Mobile Closed',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                "type" => 'EXCEPTIONS',
                'category' => [
                    'label' => 'Not Available',
                    'key' => 'not_available',
                ],
            ],
            self::NO_ANSWER => [
                'label' => 'No Answer',
                "icon" => "phone",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'No Answer',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                "type" => 'EXCEPTIONS',
                'category' => [
                    'label' => 'Not Available',
                    'key' => 'not_available',
                ],
            ],
            self::RESCHEDULE => [
                'label' => 'Reschedule',
                "icon" => "calendar",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Reschedule',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Not Available',
                    'key' => 'not_available',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::ANOTHER_VEHICLE_TYPE => [
                'label' => 'Another Vehicle Type',
                "icon" => "truck",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Another Vehicle Type',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Capacity Issue',
                    'key' => 'capacity_issue',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::VEHCILE_OVERLOADED => [
                'label' => 'Vehicle Overloaded',
                "icon" => "truck",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Vehicle Overloaded',
                'tags' => ["PICKUP_TRIP_EXCEPTION", "PICKUP_EXCEPTION"],
                'category' => [
                    'label' => 'Capacity Issue',
                    'key' => 'capacity_issue',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::CITY_CHANGED => [
                'label' => 'City Changed',
                "icon" => "map",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'City Changed',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'key' => 'location_change',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::AREA_CHANGED => [
                'label' => 'Area Changed',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Area Changed',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'key' => 'location_change',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::ROUTE_CHANGED => [
                'label' => 'Route Changed',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Route Changed',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'key' => 'location_change',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::WRONG_ASSIGNMENT => [
                'label' => 'Wrong Assignment',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Wrong Assignment',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'key' => 'location_change',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::DUPLICATE_PICKUP => [
                'label' => 'Duplicate Pickup',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Duplicate Pickup',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Cancelled',
                    'key' => 'cancelled',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::NO_SHIPMENTS => [
                'label' => 'No Shipments',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'No Shipments',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Cancelled',
                    'key' => 'cancelled',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::DROPPED_AT_RETAILR => [
                'label' => 'Dropped at Retail',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Dropped at Retail',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Cancelled',
                    'key' => 'cancelled',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::OUT_OF_PICKUP_AREA => [
                'label' => 'Out of Pickup Area',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Out of Pickup Area',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Other',
                    'key' => 'other',
                ],
                "type" => 'EXCEPTIONS',
            ]
        };
    }

    public function values(): array
    {
        return [];
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
