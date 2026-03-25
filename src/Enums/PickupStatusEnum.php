<?php

namespace twa\smsautils\Enums;

enum PickupStatusEnum: string
{
    case PENDING = 'PKPE';
    case PICKED_UP = 'PKPU';
    case CANCELLED = 'PKCN';
    case FAILED = 'PKFA';
    case DELAYED = 'PKDE';
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
    case NO_SHIPMENTS = 'PKNS';
    case DROPPED_AT_RETAILR = 'PKDR';
    case OUT_OF_PICKUP_AREA = 'PKOA';
    case NOT_READY_TO_PICKUP = 'PKNR';
    case SHIPPER_HAS_ONLY_THESE = 'PKSH';

    public function info(): array
    {
        return match ($this) {
            self::PENDING => [
                'label' => 'Pending',
                "icon" => "calendar",
                "color_bg" => "#FFF4E6", // soft orange background
                "color_text" => "#D97706", // amber-700
                'description' => 'Pending',
                'tags' => ["all"],

            ],
            self::PICKED_UP => [
                'label' => 'Picked Up',
                "icon" => "truck",
                "color_bg" => "#E0F2FE", // light blue background
                "color_text" => "#0369A1", // sky-800
                'description' => 'Picked Up',
                'tags' => ["all"],
            ],
            self::CANCELLED => [
                'label' => 'Cancelled',
                "icon" => "x",
                "color_bg" => "#FEE2E2", // light red background
                "color_text" => "#B91C1C", // red-700
                'description' => 'Cancelled',
                'tags' => ["all"],
            ],
            self::FAILED => [
                'label' => 'Failed',
                "icon" => "alert-triangle",
                "color_bg" => "#FEF2F2", // very light red background
                "color_text" => "#DC2626", // red-600
                'description' => 'Failed',
                'tags' => ["all"],
            ],
            self::DELAYED => [
                'label' => 'Delayed',
                "icon" => "clock",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Delayed',
                'tags' => ["all"],
            ],
            self::MOBILE_CLOSED => [
                'label' => 'Mobile Closed',
                "icon" => "mobile",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Mobile Closed',
                'tags' => ["EXCEPTION"],
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
                'tags' => ["EXCEPTION"],
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
                'tags' => ["EXCEPTION"],
                'category' => [
                    'label' => 'Not Available',
                    'key' => 'not_available',
                ],
            ],
            self::ANOTHER_VEHICLE_TYPE => [
                'label' => 'Another Vehicle Type',
                "icon" => "truck",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Another Vehicle Type',
                'tags' => ["EXCEPTION"],
                'category' => [
                    'label' => 'Capacity Issue',
                    'key' => 'capacity_issue',
                ],
            ],
            self::VEHCILE_OVERLOADED => [
                'label' => 'Vehicle Overloaded',
                "icon" => "truck",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Vehicle Overloaded',
                'tags' => ["EXCEPTION","MISSING_SCAN_REASON"],
                'category' => [
                    'label' => 'Capacity Issue',
                    'key' => 'capacity_issue',
                ],
            ],
            self::CITY_CHANGED => [
                'label' => 'City Changed',
                "icon" => "map",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'City Changed',
                'tags' => ["EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'key' => 'location_change',
                ],
            ],
            self::AREA_CHANGED => [
                'label' => 'Area Changed',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Area Changed',
                'tags' => ["EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'key' => 'location_change',
                ],
            ],
            self::ROUTE_CHANGED => [
                'label' => 'Route Changed',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Route Changed',
                'tags' => ["EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'key' => 'location_change',
                ],
            ],
            self::WRONG_ASSIGNMENT => [
                'label' => 'Wrong Assignment',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Wrong Assignment',
                'tags' => ["EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'key' => 'location_change',
                ],
            ],
            self::DUPLICATE_PICKUP => [
                'label' => 'Duplicate Pickup',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Duplicate Pickup',
                'tags' => ["EXCEPTION"],
                'category' => [
                    'label' => 'Cancelled',
                    'key' => 'cancelled',
                ],
            ],
            self::NO_SHIPMENTS => [
                'label' => 'No Shipments',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'No Shipments',
                'tags' => ["EXCEPTION"],
                'category' => [
                    'label' => 'Cancelled',
                    'key' => 'cancelled',
                ],
            ],
            self::DROPPED_AT_RETAILR => [
                'label' => 'Dropped at Retail',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Dropped at Retail',
                'tags' => ["EXCEPTION"],
                'category' => [
                    'label' => 'Cancelled',
                    'key' => 'cancelled',
                ],
            ],
            self::OUT_OF_PICKUP_AREA => [
                'label' => 'Out of Pickup Area',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Out of Pickup Area',
                'tags' => ["EXCEPTION"],
                'category' => [
                    'label' => 'Other',
                    'key' => 'other',
                ],
            ],
            self::NOT_READY_TO_PICKUP => [
                'label' => 'Not Ready to Pickup',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Not Ready to Pickup',
                'tags' => ["MISSING_SCAN_REASON"],
              
            ],
            self::SHIPPER_HAS_ONLY_THESE => [
                'label' => 'Shipper has only these shipments',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Shipper has only these shipments',
                'tags' => ["MISSING_SCAN_REASON"],
              
            ],
        };
    }

    public function values(): array
    {
        return [
            self::PENDING,
            self::PICKED_UP,
            self::CANCELLED,
            self::FAILED,
            self::DELAYED,
            self::MOBILE_CLOSED,
            self::NO_ANSWER,
            self::RESCHEDULE,
            self::ANOTHER_VEHICLE_TYPE,
            self::VEHCILE_OVERLOADED,
            self::CITY_CHANGED,
            self::AREA_CHANGED,
            self::ROUTE_CHANGED,
            self::WRONG_ASSIGNMENT,
            self::DUPLICATE_PICKUP,
            self::NO_SHIPMENTS,
            self::DROPPED_AT_RETAILR,
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
