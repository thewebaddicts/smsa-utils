<?php

namespace twa\smsautils\Enums;

enum ShipmentStatusTag: string
{

    case GENERAL_EXCEPTION = 'GENERAL_EXCEPTION';
    case PICKUP_EXCEPTION = 'PICKUP_EXCEPTION';
    case DELIVERY_EXCEPTION = 'DELIVERY_EXCEPTION';
    case PICKUP_TRIP_EXCEPTION = 'PICKUP_TRIP_EXCEPTION';
    case DELIVERY_TRIP_EXCEPTION = 'DELIVERY_TRIP_EXCEPTION';
    case EXCEPTION = 'EXCEPTION';
    case WORKFLOW = 'WORKFLOW';
    case PROBLEMATIC_DEBRIEF_EXCEPTION = 'PROBLEMATIC_DEBRIEF_EXCEPTION';
    case HOLD_EXCEPTION = 'HOLD_EXCEPTION';
    case ATTEMPTED = 'ATTEMPTED';
    case REFUSED = 'REFUSED';
    case WORKFLOW_TRIP_EXCEPTION = 'WORKFLOW_TRIP_EXCEPTION';
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
