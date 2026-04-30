<?php

namespace twa\smsautils\Enums;

enum ShipmentStatusTag: string
{

    case TRIP_EXCEPTION = 'TRIP_EXCEPTION';
    case PICKUP_EXCEPTION = 'PICKUP_EXCEPTION';
    case DELIVERY_EXCEPTION = 'DELIVERY_EXCEPTION';
    case ALL = 'ALL';

    public function getList(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'label' => $case->value,
                'value' => $case->value,
            ];
        })->toArray();
    }
}
