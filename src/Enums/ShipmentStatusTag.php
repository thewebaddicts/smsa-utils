<?php

namespace twa\smsautils\Enums;

enum ShipmentStatusTag: string
{

    case GENERAL_AWB_EXCEPTION = 'GENERAL_AWB_EXCEPTION';
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

    /**
     * Domain this tag is used for in shipment status configuration.
     */
    public function entityType(): string
    {
        return match ($this) {
            self::PICKUP_EXCEPTION,
            self::PICKUP_TRIP_EXCEPTION => 'pickup_request',
            default => 'awb',
        };
    }

    /**
     * Short explanation of where / when this tag applies (for UI and API docs).
     */
    public function description(): string
    {
        return match ($this) {
            self::GENERAL_AWB_EXCEPTION => 'This is happening on the mobile app then putting a generaral exception for the awb on pickup or delivery',
            self::PICKUP_EXCEPTION => 'First Mile flow: on pickup request where the nb of packages of the pickup did not match the nb of scanned awbs',
            self::DELIVERY_EXCEPTION => 'Last Mile flow: problems while attempting or completing delivery to the consignee.',
            self::PICKUP_TRIP_EXCEPTION => 'Pickup trip: courier route or on-the-road issues during pickup execution.',
            self::DELIVERY_TRIP_EXCEPTION => 'Delivery trip: courier route or on-the-road issues during delivery execution.',
            self::EXCEPTION => 'Exception on shipment details page.',
            self::WORKFLOW => 'Workflow builder: statuses that may drive or appear in workflow logic statuses dropdown.',
            self::WORKFLOW_TRIP_EXCEPTION => 'Grouping for workflow logic statuses dropdown.',

            self::PROBLEMATIC_DEBRIEF_EXCEPTION => 'Debrief / closure: lost, closed, or similar outcomes that need exception handling.',
            self::HOLD_EXCEPTION => 'On-hold pipeline: customs, operational, or other holds that block normal progress.',
            self::ATTEMPTED => 'grouping for attempted statuses dropdown in workflow logic.',
            self::REFUSED => 'grouping for refused statuses dropdown in workflow logic.',
            self::ALL => 'Universal tag: status is not restricted to a single exception subtype (e.g. reporting or global lists).',
        };
    }

    public function getList(): array
    {
        return collect(self::cases())->map(function ($case) {
            return [
                'label' => $case->value,
                'value' => $case->value,
                'type' => $case->entityType(),
                'description' => $case->description(),
            ];
        })->toArray();
    }
}
