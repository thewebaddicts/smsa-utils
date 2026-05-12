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



    public function info(): array
    {
        return match ($this) {

            self::PICKUP_CREATED => [
                'label' => 'Pending',
                'label_ar' => 'قيد الإنتظار',
                "icon" => "calendar",
                "color_bg" => "#FFF4E6", // soft orange background
                "color_text" => "#D97706", // amber-700
                'description' => 'Pickup Created',
                'description_ar' => 'تم إنشاء الاستلام',
                'tags' => [],
                "type" => null,
            ],
            self::PICKUP_CANCELLED => [
                'label' => 'Cancelled',
                'label_ar' => 'ملغى',
                "icon" => "x",
                "color_bg" => "#FEE2E2", // light red background
                "color_text" => "#B91C1C", // red-700
                'description' => 'Cancelled',
                'description_ar' => 'ملغى',
                'tags' => [],
                "type" => null,
            ],
            self::PICKUP_FAILED => [
                'label' => 'Failed',
                'label_ar' => 'فشل',
                "icon" => "alert-triangle",
                "color_bg" => "#FEF2F2", // very light red background
                "color_text" => "#DC2626", // red-600
                'description' => 'Failed',
                'description_ar' => 'فشل',
                'tags' => [],
                "type" => null,
            ],
            self::PICKUP_DELAYED => [
                'label' => 'Delayed',
                'label_ar' => 'متأخر',
                "icon" => "clock",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Delayed',
                'description_ar' => 'متأخر',
                'tags' => [],
                "type" => null,
            ],
            self::PICKUP_PICKED_UP => [
                'label' => 'Picked Up',
                'label_ar' => 'تم الاستلام',
                "icon" => "truck",
                "color_bg" => "#E0F2FE", // light blue background
                "color_text" => "#0369A1", // sky-800
                'description' => 'Picked Up',
                'description_ar' => 'تم الاستلام',
                'tags' => [],
                "type" => null,
            ],



            self::NOT_READY_TO_PICKUP => [
                'label' => 'Not Ready to Pickup',
                'label_ar' => 'غير جاهز للاستلام',
                'icon' => 'map',
                'color_bg' => '#FEF9C3',
                'color_text' => '#92400E',
                'description' => 'Not Ready to Pickup',
                'description_ar' => 'غير جاهز للاستلام',
                'tags' => ['PICKUP_EXCEPTION'],
                "type" => 'EXCEPTIONS',
            ],
            self::SHIPPER_HAS_ONLY_THESE => [
                'label' => 'Shipper has only these shipments',
                'label_ar' => 'لدى المرسل هذه الشحنات فقط',
                'icon' => 'map',
                'color_bg' => '#FEF9C3',
                'color_text' => '#92400E',
                'description' => 'Shipper has only these shipments',
                'description_ar' => 'لدى المرسل هذه الشحنات فقط',
                'tags' => ['PICKUP_EXCEPTION'],
                "type" => 'EXCEPTIONS',
            ],

            self::MOBILE_CLOSED => [
                'label' => 'Mobile Closed',
                'label_ar' => 'الهاتف المحمول مغلق',
                "icon" => "mobile",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Mobile Closed',
                'description_ar' => 'الهاتف المحمول مغلق',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                "type" => 'EXCEPTIONS',
                'category' => [
                    'label' => 'Not Available',
                    'label_ar' => 'غير متاح',
                    'key' => 'not-available',
                ],
            ],
            self::NO_ANSWER => [
                'label' => 'No Answer',
                'label_ar' => 'لا يوجد رد',
                "icon" => "phone",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'No Answer',
                'description_ar' => 'لا يوجد رد',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                "type" => 'EXCEPTIONS',
                'category' => [
                    'label' => 'Not Available',
                    'label_ar' => 'غير متاح',
                    'key' => 'not-available',
                ],
            ],
            self::RESCHEDULE => [
                'label' => 'Reschedule',
                'label_ar' => 'إعادة جدولة',
                "icon" => "calendar",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Reschedule',
                'description_ar' => 'إعادة جدولة',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Not Available',
                    'label_ar' => 'غير متاح',
                    'key' => 'not-available',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::ANOTHER_VEHICLE_TYPE => [
                'label' => 'Another Vehicle Type',
                'label_ar' => 'نوع مركبة آخر',
                "icon" => "truck",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Another Vehicle Type',
                'description_ar' => 'نوع مركبة آخر',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Capacity Issue',
                    'label_ar' => 'مشكلة في السعة',
                    'key' => 'capacity_issue',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::VEHCILE_OVERLOADED => [
                'label' => 'Vehicle Overloaded',
                'label_ar' => 'المركبة ممتلئة',
                "icon" => "truck",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'Vehicle Overloaded',
                'description_ar' => 'المركبة ممتلئة',
                'tags' => ["PICKUP_TRIP_EXCEPTION", "PICKUP_EXCEPTION"],
                'category' => [
                    'label' => 'Capacity Issue',
                    'label_ar' => 'مشكلة في السعة',
                    'key' => 'capacity_issue',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::CITY_CHANGED => [
                'label' => 'City Changed',
                'label_ar' => 'تغيير المدينة',
                "icon" => "map",
                "color_bg" => "#FEF9C3", // soft yellow background
                "color_text" => "#92400E", // amber-800
                'description' => 'City Changed',
                'description_ar' => 'تغيير المدينة',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'label_ar' => 'تغيير الموقع',
                    'key' => 'location_change',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::AREA_CHANGED => [
                'label' => 'Area Changed',
                'label_ar' => 'تغيير المنطقة',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Area Changed',
                'description_ar' => 'تغيير المنطقة',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'label_ar' => 'تغيير الموقع',
                    'key' => 'location_change',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::ROUTE_CHANGED => [
                'label' => 'Route Changed',
                'label_ar' => 'تغيير المسار',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Route Changed',
                'description_ar' => 'تغيير المسار',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'label_ar' => 'تغيير الموقع',
                    'key' => 'location_change',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::WRONG_ASSIGNMENT => [
                'label' => 'Wrong Assignment',
                'label_ar' => 'تعيين خاطئ',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Wrong Assignment',
                'description_ar' => 'تعيين خاطئ',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Location Change',
                    'label_ar' => 'تغيير الموقع',
                    'key' => 'location_change',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::DUPLICATE_PICKUP => [
                'label' => 'Duplicate Pickup',
                'label_ar' => 'استلام مكرر',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Duplicate Pickup',
                'description_ar' => 'استلام مكرر',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Cancelled',
                    'label_ar' => 'ملغى',
                    'key' => 'cancelled',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::NO_SHIPMENTS => [
                'label' => 'No Shipments',
                'label_ar' => 'لا توجد شحنات',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'No Shipments',
                'description_ar' => 'لا توجد شحنات',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Cancelled',
                    'label_ar' => 'ملغى',
                    'key' => 'cancelled',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::DROPPED_AT_RETAILR => [
                'label' => 'Dropped at Retail',
                'label_ar' => 'تم الإيداع في نقطة البيع',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Dropped at Retail',
                'description_ar' => 'تم الإيداع في نقطة البيع',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Cancelled',
                    'label_ar' => 'ملغى',
                    'key' => 'cancelled',
                ],
                "type" => 'EXCEPTIONS',
            ],
            self::OUT_OF_PICKUP_AREA => [
                'label' => 'Out of Pickup Area',
                'label_ar' => 'خارج منطقة الاستلام',
                "icon" => "map",
                "color_bg" => "#FEF9C3",
                "color_text" => "#92400E", // amber-800
                'description' => 'Out of Pickup Area',
                'description_ar' => 'خارج منطقة الاستلام',
                'tags' => ["PICKUP_TRIP_EXCEPTION"],
                'category' => [
                    'label' => 'Other',
                    'label_ar' => 'أخرى',
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
