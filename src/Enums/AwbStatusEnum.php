<?php

namespace twa\smsautils\Enums;

enum AwbStatusEnum: string
{
    case CREATED = 'SHCR';
    case PICKED_UP = 'SHPU';
    case RECEIVED_OPERATION = 'SHOR';
    case CRN_IN = 'SHCI';
    case CRN_OUT = 'SHCO';
    case SHELF_IN = 'SHSI';
    case SHELF_OUT = 'SHSO';
    case HUB_IN = 'SHHI';
    case HUB_OUT = 'SHHO';
    case OPERATION_INBOUND = 'SHOI';
    case OPERATION_OUTBOUND = 'SHOO';
    case DELIVERED = 'SHDL';
    case REVERSE_DELIEVERY = 'SHRD';
    case DEBRIEFED_OPERATION = 'SHDO';
    case DEBRIEFED_FINANCE = 'SHDF';
    case CLOSED = 'SHCL';
    case ADDRESS_CHANGED = 'SHAU';
    case ADDRESS_VALIDATED = 'SHAV';
    case UPDATED_DIMENSIONS = 'SHDU';
    case UPDATED_WEIGHT = 'SHWU';
    case CANCELLED = 'SHCN';
    case CHANGE_ROUTE = 'SHRC';
    case OUT_FOR_DELIVERY = 'SHOD';
    case SMS_SENT = 'SHSM';
    case SHIPPER_CANCELED = 'SHSC';
    case IN_TRANSIT = 'SHIT';
    case GATEWAY_INBOUND = 'SHGI';
    case GATEWAY_OUTBOUND = 'SHGO';
    case HOLD_FOR_PICKUP = 'SHHP';
    case HOLD = 'SHOH'; //was DEX
    case RELEASE_HOLD = 'SHRL';
    case HOLD_CUSTOMS = 'SHCH'; //was DEX
    case RELEASE_CUSTOMS = 'SHRC';
    case RETURN_IN = 'SHRI';
    case RETURN_OUT = 'SHRO';
    case RETURN_TO_SHIPPER = 'SHRS';
    case SCAN_RUNSHEET = 'SHRA';
    case SCAN_RUNSHEET_VERIFIED = 'SHRU';


    case NOT_AVAILABLE_MOBILE_CLOSED  = 'ATMC';
    case NOT_AVAILABLE_NO_ANSWER  = 'ATNA';
    case NOT_AVAILABLE_RESCHEDULE  = 'ATRS';
    case NOT_AVAILABLE_TRAVELING  = 'ATTR';

    case NOT_AVAILABLE_WRONG_PHONE = 'ATWN';
    case NOT_AVAILABLE_WRONG_CUSTOMER = 'ATWC';
    case NOT_AVAILABLE_WRONG_CITY = 'ATLC';
    case NOT_AVAILABLE_ROUTE = 'ATLR'; //to be checked by hovig
    case NOT_AVAILABLE_OUT_OF_AREA = 'ATOA';

    case HOLD_PICKUP = 'ATLC';

    case NOT_PICKED_UP_RESCHEDULE = 'NPRS';
    case NOT_PICKED_UP_NO_ANSWER = 'NPNA';
    case NOT_PICKED_UP_ADDRESS_CHANGED = 'NPLR';


    case REFUSED_OPEN_SHIPMENT  = 'RFOS';
    case REFUSED_MONEY  = 'RFMO';
    case REFUSED_ALREADY_RECEIVED  = 'RFAR';
    case REFUSED_NO_LONGER_NEEDED = 'RFNN';
    case REFUSED_DELAYED = 'RFDL'; // was exp


    case OVERAGE = 'SHOV'; // was exp
    case ATTEMPTED = 'SHAT';
    case REFUSED = 'SHRF';


    case DAMAGED = 'EXDM'; // was exp
    case LOST = 'EXLO';



    public function info(): array
    {
        return match ($this) {
            self::CREATED => [
                'label' => 'Created',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Shipment has been created in the system',
            ],
            self::PICKED_UP => [
                'label' => 'Picked Up',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment has been picked up from sender',
            ],
            self::RECEIVED_OPERATION => [
                'label' => 'Received',
                'icon' => 'inbox',
                'color_bg' => '#f0f9eb',
                'color_text' => '#2e7d32',
                'description' => 'Shipment received in operations',
            ],
            self::SHELF_IN => [
                'label' => 'Shelf In',
                'icon' => 'archive',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Shipment placed in storage shelf',
            ],
            self::SHELF_OUT => [
                'label' => 'Shelf Out',
                'icon' => 'archive',
                'color_bg' => '#e1f5fe',
                'color_text' => '#0288d1',
                'description' => 'Shipment removed from storage shelf',
            ],
            self::HUB_IN => [
                'label' => 'Received Hub',
                'icon' => 'home',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Shipment arrived at hub facility',
            ],
            self::HUB_OUT => [
                'label' => 'Hub Departure',
                'icon' => 'log-out',
                'color_bg' => '#ffe0b2',
                'color_text' => '#ef6c00',
                'description' => 'Shipment departed from hub facility',
            ],
            self::OPERATION_INBOUND => [
                'label' => 'Received Operations',
                'icon' => 'download',
                'color_bg' => '#dcedc8',
                'color_text' => '#558b2f',
                'description' => 'Shipment received by operations team',
            ],
            self::OPERATION_OUTBOUND => [
                'label' => 'Operations Out',
                'icon' => 'upload',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Shipment dispatched by operations team',
            ],
            self::DELIVERED => [
                'label' => 'Delivered',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully delivered',
            ],
            self::REVERSE_DELIEVERY => [
                'label' => 'Reverse Delivery',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully reversed',
            ],
            self::DEBRIEFED_OPERATION => [
                'label' => 'Debrief Operation',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully reversed',
            ],
            self::DEBRIEFED_FINANCE => [
                'label' => 'Debrief Finance',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully reversed',
            ],
            self::ADDRESS_CHANGED => [
                'label' => 'Address changed',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#15c0a4ff',
                'description' => 'Shipment created in system',
            ],
            self::ADDRESS_VALIDATED => [
                'label' => 'Address validated',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#d482d8ff',
                'description' => 'Shipment created in system',
            ],
            self::UPDATED_DIMENSIONS => [
                'label' => 'Updated Dimensions',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::UPDATED_WEIGHT => [
                'label' => 'Updated Weight',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::CANCELLED => [
                'label' => 'Cancelled',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Shipment cancelled',
            ],
            self::CHANGE_ROUTE => [
                'label' => 'Change Route',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::OUT_FOR_DELIVERY => [
                'label' => 'Out for Delivery',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment is out with courier',
            ],
            self::SMS_SENT => [
                'label' => 'SMS Sent',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment is out with courier',
            ],
            self::SHIPPER_CANCELED => [
                'label' => 'Cancelled by shipper',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment cancelled by shipper',
            ],
            self::IN_TRANSIT => [
                'label' => 'In Transit',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in transit',
            ],
            self::GATEWAY_INBOUND => [
                'label' => 'Gateway inbound',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in gateway',
            ],
            self::GATEWAY_OUTBOUND => [
                'label' => 'Gateway Outbound',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in gateway',
            ],
            self::HOLD_FOR_PICKUP => [
                'label' => 'Hold For Pickup',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in gateway',
            ],
            self::HOLD => [
                'label' => 'On Hold',
                'icon' => 'pause-circle',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment placed on hold',
            ],
            self::RELEASE_HOLD => [
                'label' => 'Release Hold',
                'icon' => 'pause-circle',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment placed on hold',
            ],
            self::HOLD_CUSTOMS => [
                'label' => 'On Hold - Customs',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment on hold for customs clearance',
            ],
            self::RELEASE_CUSTOMS => [
                'label' => 'Release Customs',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment on hold for customs clearance',
            ],
            self::RETURN_IN => [
                'label' => 'Return In',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment Returned in',
            ],
            self::RETURN_OUT => [
                'label' => 'Return Out',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment Returned out',
            ],
            self::RETURN_TO_SHIPPER => [
                'label' => 'Return to shipper',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment Returned out',
            ],
            self::SCAN_RUNSHEET => [
                'label' => 'Scan Runsheet',
                'icon' => 'clipboard-list',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment scanned and assigned to runsheet',
            ],
            self::SCAN_RUNSHEET_VERIFIED => [
                'label' => 'Runsheet Verified',
                'icon' => 'clipboard-check',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment verification completed on runsheet',
            ],
            self::NOT_AVAILABLE_MOBILE_CLOSED => [
                'label' => 'Mobile Closed',
                'icon' => 'phone-off',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient mobile phone is closed, could not contact',
            ],
            self::NOT_AVAILABLE_NO_ANSWER => [
                'label' => 'No Answer',
                'icon' => 'phone-missed',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient did not answer the call',
            ],
            self::NOT_AVAILABLE_RESCHEDULE => [
                'label' => 'Reschedule',
                'icon' => 'calendar',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Delivery attempt needs to be rescheduled',
            ],
            self::NOT_AVAILABLE_TRAVELING => [
                'label' => 'Traveling',
                'icon' => 'map-pin',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient is traveling, cannot receive shipment',
            ],
            self::NOT_AVAILABLE_WRONG_PHONE => [
                'label' => 'Wrong Phone Number',
                'icon' => 'phone-slash',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery could not be completed due to wrong phone number',
            ],
            self::NOT_AVAILABLE_WRONG_CUSTOMER => [
                'label' => 'Wrong Customer',
                'icon' => 'user-x',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery could not be completed due to wrong recipient',
            ],
            self::NOT_AVAILABLE_WRONG_CITY => [
                'label' => 'Wrong City',
                'icon' => 'map',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Shipment address does not match city',
            ],
            self::NOT_AVAILABLE_ROUTE => [
                'label' => 'Route Issue',
                'icon' => 'map-signs',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery route needs verification',
            ],
            self::NOT_AVAILABLE_OUT_OF_AREA => [
                'label' => 'Out of Delivery Area',
                'icon' => 'map-pin-off',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery address is outside the service area',
            ],
            self::HOLD_PICKUP => [
                'label' => 'Hold for Pickup',
                'icon' => 'archive',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Shipment is held for pickup by recipient',
            ],

            self::ATTEMPTED => [
                'label' => 'Attempted',
                'icon' => 'alert-circle',
                'color_bg' => '#fff4e5',
                'color_text' => '#b36b00',
                'description' => 'Delivery attempt made',
            ],
            self::REFUSED => [
                'label' => 'Refused',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Recipient refused the shipment',
            ],

            // NOT PICKED UP STATUSES
            self::NOT_PICKED_UP_RESCHEDULE => [
                'label' => 'Reschedule Pickup',
                'icon' => 'calendar',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Pickup attempt needs to be rescheduled',
            ],
            self::NOT_PICKED_UP_NO_ANSWER => [
                'label' => 'No Answer',
                'icon' => 'phone-missed',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient did not answer during pickup attempt',
            ],
            self::NOT_PICKED_UP_ADDRESS_CHANGED => [
                'label' => 'Address Changed',
                'icon' => 'map-pin',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Pickup could not be completed due to address change',
            ],

            self::REFUSED_OPEN_SHIPMENT => [
                'label' => 'Refused to Open Shipment',
                'icon' => 'x-circle',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Recipient refused to open the shipment before delivery',
            ],
            self::REFUSED_NO_LONGER_NEEDED => [
                'label' => 'Refused – No Longer Needed',
                'icon' => 'x-circle',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Recipient refused shipment, no longer needed',
            ],
            self::OVERAGE => [
                'label' => 'Overage',
                'icon' => 'plus-circle',
                'color_bg' => '#fbe9e7',
                'color_text' => '#bf360c',
                'description' => 'Extra shipment found',
            ],

            self::DAMAGED => [
                'label' => 'Damaged',
                'icon' => 'alert-octagon',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment damaged',
            ],

            self::REFUSED_DELAYED => [
                'label' => 'Refused - Delayed',
                'icon' => 'clock',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Refused delivery due to delay',
            ],

            self::CANCELLED => [
                'label' => 'Cancelled',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Shipment cancelled',
            ],


            self::LOST => [
                'label' => 'Lost',
                'icon' => 'help-circle',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment lost in transit',
            ],



            self::CLOSED => [
                'label' => 'Closed',
                'icon' => 'lock',
                'color_bg' => '#eeeeee',
                'color_text' => '#424242',
                'description' => 'Shipment closed',
            ],

            self::CRN_IN => [
                'label' => 'CRN In',
                'icon' => 'check-circle',
                'color_bg' => '#c8e6c9',
                'color_text' => '#000000',
                'description' => 'AWB has been scanned successfully'
            ],
            self::CRN_OUT => [
                'label' => 'CRN Out',
                'icon' => 'check-circle',
                'color_bg' => '#c8e6c9',
                'color_text' => '#000000',
                'description' => 'AWB has been scanned successfully'
            ]
        };
    }

    public function  infoHub(): array
    {
        return match ($this) {
            self::CREATED => [
                'label' => 'Created',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Shipment has been created in the system',
            ],
            self::PICKED_UP => [
                'label' => 'Picked Up',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment has been picked up from sender',
            ],
            self::RECEIVED_OPERATION => [
                'label' => 'Received',
                'icon' => 'inbox',
                'color_bg' => '#f0f9eb',
                'color_text' => '#2e7d32',
                'description' => 'Shipment received in operations',
            ],

            self::ATTEMPTED => [
                'label' => 'Attempted',
                'icon' => 'alert-circle',
                'color_bg' => '#fff4e5',
                'color_text' => '#b36b00',
                'description' => 'Delivery attempt made',
            ],
            self::SHELF_IN => [
                'label' => 'Shelf In',
                'icon' => 'archive',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Shipment placed in storage shelf',
            ],
            self::SHELF_OUT => [
                'label' => 'Shelf Out',
                'icon' => 'archive',
                'color_bg' => '#e1f5fe',
                'color_text' => '#0288d1',
                'description' => 'Shipment removed from storage shelf',
            ],
            self::HUB_IN => [
                'label' => 'Received Hub',
                'icon' => 'home',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Shipment arrived at hub facility',
            ],
            self::HUB_OUT => [
                'label' => 'Hub Departure',
                'icon' => 'log-out',
                'color_bg' => '#ffe0b2',
                'color_text' => '#ef6c00',
                'description' => 'Shipment departed from hub facility',
            ],
            self::OPERATION_INBOUND => [
                'label' => 'Received Operations',
                'icon' => 'download',
                'color_bg' => '#dcedc8',
                'color_text' => '#558b2f',
                'description' => 'Shipment received by operations team',
            ],
            self::OPERATION_OUTBOUND => [
                'label' => 'Operations Out',
                'icon' => 'upload',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Shipment dispatched by operations team',
            ],
            self::DELIVERED => [
                'label' => 'Delivered',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully delivered',
            ],
            self::REVERSE_DELIEVERY => [
                'label' => 'Reverse Delivery',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully reversed',
            ],
            self::DEBRIEFED_OPERATION => [
                'label' => 'Debrief Operation',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully reversed',
            ],
            self::DEBRIEFED_FINANCE => [
                'label' => 'Debrief Finance',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully reversed',
            ],
            self::ADDRESS_CHANGED => [
                'label' => 'Address changed',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#15c0a4ff',
                'description' => 'Shipment created in system',
            ],
            self::ADDRESS_VALIDATED => [
                'label' => 'Address validated',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#d482d8ff',
                'description' => 'Shipment created in system',
            ],
            self::UPDATED_DIMENSIONS => [
                'label' => 'Updated Dimensions',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::UPDATED_WEIGHT => [
                'label' => 'Updated Weight',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::CANCELLED => [
                'label' => 'Cancelled',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Shipment cancelled',
            ],
            self::CHANGE_ROUTE => [
                'label' => 'Change Route',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::OUT_FOR_DELIVERY => [
                'label' => 'Pending Scan',
                'icon' => 'fa-hourglass-half',
                'color_bg' => '#9e9e9e',
                'color_text' => '#ffffff',
                'description' => 'MAWB is out'
            ],
            self::SMS_SENT => [
                'label' => 'SMS Sent',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment is out with courier',
            ],
            self::SHIPPER_CANCELED => [
                'label' => 'Cancelled by shipper',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment cancelled by shipper',
            ],
            self::IN_TRANSIT => [
                'label' => 'In Transit',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in transit',
            ],
            self::GATEWAY_INBOUND => [
                'label' => 'Gateway inbound',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in gateway',
            ],
            self::GATEWAY_OUTBOUND => [
                'label' => 'Gateway Outbound',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in gateway',
            ],
            self::HOLD_FOR_PICKUP => [
                'label' => 'Hold For Pickup',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'shipment in gateway',
            ],
            self::HOLD => [
                'label' => 'On Hold',
                'icon' => 'pause-circle',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment placed on hold',
            ],
            self::RELEASE_HOLD => [
                'label' => 'Release Hold',
                'icon' => 'pause-circle',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment placed on hold',
            ],
            self::HOLD_CUSTOMS => [
                'label' => 'On Hold - Customs',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment on hold for customs clearance',
            ],
            self::RELEASE_CUSTOMS => [
                'label' => 'Release Customs',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment on hold for customs clearance',
            ],
            self::RETURN_IN => [
                'label' => 'Return In',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment Returned in',
            ],
            self::RETURN_OUT => [
                'label' => 'Return Out',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment Returned out',
            ],
            self::RETURN_TO_SHIPPER => [
                'label' => 'Return to shipper',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment Returned out',
            ],
            self::SCAN_RUNSHEET => [
                'label' => 'Scan Runsheet',
                'icon' => 'clipboard-list',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment scanned and assigned to runsheet',
            ],
            self::SCAN_RUNSHEET_VERIFIED => [
                'label' => 'Runsheet Verified',
                'icon' => 'clipboard-check',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment verification completed on runsheet',
            ],
            self::NOT_AVAILABLE_MOBILE_CLOSED => [
                'label' => 'Mobile Closed',
                'icon' => 'phone-off',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient mobile phone is closed, could not contact',
            ],
            self::NOT_AVAILABLE_NO_ANSWER => [
                'label' => 'No Answer',
                'icon' => 'phone-missed',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient did not answer the call',
            ],
            self::NOT_AVAILABLE_RESCHEDULE => [
                'label' => 'Reschedule',
                'icon' => 'calendar',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Delivery attempt needs to be rescheduled',
            ],
            self::NOT_AVAILABLE_TRAVELING => [
                'label' => 'Traveling',
                'icon' => 'map-pin',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient is traveling, cannot receive shipment',
            ],
            self::NOT_AVAILABLE_WRONG_PHONE => [
                'label' => 'Wrong Phone Number',
                'icon' => 'phone-slash',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery could not be completed due to wrong phone number',
            ],
            self::NOT_AVAILABLE_WRONG_CUSTOMER => [
                'label' => 'Wrong Customer',
                'icon' => 'user-x',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery could not be completed due to wrong recipient',
            ],
            self::NOT_AVAILABLE_WRONG_CITY => [
                'label' => 'Wrong City',
                'icon' => 'map',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Shipment address does not match city',
            ],
            self::NOT_AVAILABLE_ROUTE => [
                'label' => 'Route Issue',
                'icon' => 'map-signs',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery route needs verification',
            ],
            self::NOT_AVAILABLE_OUT_OF_AREA => [
                'label' => 'Out of Delivery Area',
                'icon' => 'map-pin-off',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Delivery address is outside the service area',
            ],
            self::HOLD_PICKUP => [
                'label' => 'Hold for Pickup',
                'icon' => 'archive',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Shipment is held for pickup by recipient',
            ],
            self::UPDATED_DIMENSIONS => [
                'label' => 'Updated Dimensions',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::UPDATED_WEIGHT => [
                'label' => 'Updated Weight',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#82add8ff',
                'description' => 'Shipment created in system',
            ],
            self::REFUSED => [
                'label' => 'Refused',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Recipient refused the shipment',
            ],

            // NOT PICKED UP STATUSES
            self::NOT_PICKED_UP_RESCHEDULE => [
                'label' => 'Reschedule Pickup',
                'icon' => 'calendar',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Pickup attempt needs to be rescheduled',
            ],
            self::NOT_PICKED_UP_NO_ANSWER => [
                'label' => 'No Answer',
                'icon' => 'phone-missed',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Recipient did not answer during pickup attempt',
            ],
            self::NOT_PICKED_UP_ADDRESS_CHANGED => [
                'label' => 'Address Changed',
                'icon' => 'map-pin',
                'color_bg' => '#fff3e0',
                'color_text' => '#fb8c00',
                'description' => 'Pickup could not be completed due to address change',
            ],

            // REFUSED STATUSES
            self::REFUSED_OPEN_SHIPMENT => [
                'label' => 'Refused to Open Shipment',
                'icon' => 'x-circle',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Recipient refused to open the shipment before delivery',
            ],
            self::REFUSED_NO_LONGER_NEEDED => [
                'label' => 'Refused – No Longer Needed',
                'icon' => 'x-circle',
                'color_bg' => '#ffcdd2',
                'color_text' => '#c62828',
                'description' => 'Recipient refused shipment, no longer needed',
            ],
            self::OVERAGE => [
                'label' => 'Overage',
                'icon' => 'plus-circle',
                'color_bg' => '#fbe9e7',
                'color_text' => '#bf360c',
                'description' => 'Extra shipment found',
            ],



            self::ATTEMPTED => [
                'label' => 'Attempted',
                'icon' => 'alert-circle',
                'color_bg' => '#fff4e5',
                'color_text' => '#b36b00',
                'description' => 'Delivery attempt made',
            ],

            self::REFUSED_DELAYED => [
                'label' => 'Refused - Delayed',
                'icon' => 'clock',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Refused delivery due to delay',
            ],

            self::HOLD_CUSTOMS => [
                'label' => 'On Hold - Customs',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment on hold for customs clearance',
            ],

            self::DAMAGED => [
                'label' => 'Damaged',
                'icon' => 'alert-octagon',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment damaged',
            ],

            self::LOST => [
                'label' => 'Lost',
                'icon' => 'help-circle',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment lost in transit',
            ],

            self::CLOSED => [
                'label' => 'Closed',
                'icon' => 'lock',
                'color_bg' => '#eeeeee',
                'color_text' => '#424242',
                'description' => 'Shipment closed',
            ],


            self::CRN_IN => [
                'label' => 'CRN In',
                'icon' => 'check-circle',
                'color_bg' => '#c8e6c9',
                'color_text' => '#000000',
                'description' => 'AWB has been scanned successfully'
            ],
            self::CRN_OUT => [
                'label' => 'CRN Out',
                'icon' => 'check-circle',
                'color_bg' => '#c8e6c9',
                'color_text' => '#000000',
                'description' => 'AWB has been scanned successfully'
            ]
        };
    }
}