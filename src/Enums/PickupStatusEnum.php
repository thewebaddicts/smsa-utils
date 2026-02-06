<?php

namespace twa\smsautils\Enums;

enum PickupStatusEnum: string
{
    case PENDING = 'pending';
    case PICKED_UP = 'picked_up';
    case CANCELLED = 'cancelled';
    case FAILED = 'failed';
    case DELAYED = 'delayed';
    case MOBILE_CLOSED = 'mobile_closed';
    case NO_ANSWER = 'no_answer';
    case RESCHEDULE = 'reschedule';
    case ANOTHER_VEHICLE_TYPE = 'another_vehicle_type';
    case VEHCILE_OVERLOADED = 'vechile_overloaded';
    case CITY_CHANGED = 'city_changed';
    case AREA_CHANGED = 'area_changed';
    case ROUTE_CHANGED = 'route_changed';
    case WRONG_ASSIGNMENT = 'wrong_assignment';
    case DUPLICATE_PICKUP = 'duplicate_pickup';
    case NO_SHIPMENTS = 'no_shipments';
    case DROPPED_AT_RETAILR = 'dropped_at_retail';
    case OUT_OF_PICKUP_AREA = 'out_of_pickup_area';

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
                'tags' => ["EXCEPTION"],
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
