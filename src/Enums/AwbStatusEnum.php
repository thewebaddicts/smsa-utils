<?php

namespace twa\smsautils\Enums;

enum AwbStatusEnum: string
{
    case CREATED = 'CR';
    case PICKED_UP = 'PU';
    case RECEIVED_OPERATION = 'R100';
    case SHELVED = 'SHEL10';
    case SCAN_RUNSHEET = 'SC100';
    case SCAN_RUNSHEET_VERIFIED = 'SC101';
    case OUT_FOR_DELIVERY = 'OD';
    case DELIVERED = 'DL';

    case WMS_SHELVE_ASSIGNED = 'WMS-SA';
    case WMS_BIN_ASSIGNED = 'WMS-BA';

    case WMS_PICKED = 'WMS_PK';
    case WMS_OUTBOUND = 'WMS_OB';

    case RETRIEVED = 'RETR10';



        // case PENDING = 'pending';

        // case SCANNED = 'SC';

        // ready for dispatch
        // missing from shelf
        // on hold
        // EXCEPTIONS

        // case ATTEMPTED = 'EX-100'; //To be Removed

    case ATTEMPTED = 'DEX-ATT';
    case ATTEMPTED_WRONG_ADDRESS = 'DEX-ATT-101';
    case ATTEMPTED_UNREACHABLE = 'DEX-ATT-102';
    case ATTEMPTED_UNAVAILABLE = 'DEX-ATT-103';

        // case REFUSED = 'EX-101'; // to be removed

    case REFUSED = 'DEX-RFS';
    case REFUSED_WRONG_PACKAGE = 'DEX-RFS-101';
    case REFUSED_DELAYED = 'DEX-RFS-102';
    case REFUSED_DAMAGED = 'DEX-RFS-103';

    case DEBRIEF_OUTSTANDING = 'DB-OUT';
        // case CANCELLED = 'cancelled';




    case HOLD = 'DEX-HL';
    case HOLD_PAYMENT_ISSUE   = 'DEX-HL-101';
    case HOLD_DOCUMENTS       = 'DEX-HL-102';
    case HOLD_CUSTOMS         = 'DEX-HL-103';

        // case DEBRIEF_OUTSTANDING = 'DEX-OTD';
        // case CANCELLED = 'DEX-CNL';
        // case HOLD = 'DEX-HLD';

    case CUSTOMS_HOLD = 'DEX-CUS';
    case DAMAGED = 'DEX-DMG';
    case STOLEN = 'DEX-STL';
    case LOST = 'DEX-LST';
    case WEATHER = 'DEX-WTH';
    case SECURITY = 'DEX-SEC';

    case OVERAGE = 'DEX-OVG';
    case CANCELLED_CUSTOMER_REQUEST = 'DEX-CA-101';
    case CANCELLED_OPERATIONAL_ISSUE = 'DEX-CA-102';

    case MISSING_SHELVE = 'MIS10';

    case WMS_MISSING_SHELVE = 'WMS-MS';

    case CLOSED = 'CL';

    case CANCELLED = 'CLD';

        //nourhane
    case ARRIVED = 'AR100';
        // To be Removed

    case OPERATIONS_QC = 'OP-QC';



    public function info(): array
    {
        return match ($this) {
            self::CREATED => [
                'label' => 'Created',
                'icon' => 'file-plus',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Shipment created in system',
            ],
            self::PICKED_UP => [
                'label' => 'Picked Up',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment picked up',
            ],
            self::RECEIVED_OPERATION => [
                'label' => 'Received',
                'icon' => 'inbox',
                'color_bg' => '#f0f9eb',
                'color_text' => '#2e7d32',
                'description' => 'Shipment received in operations',
            ],
            self::SHELVED => [
                'label' => 'In Storage',
                'icon' => 'archive',
                'color_bg' => '#fffde7',
                'color_text' => '#f57f17',
                'description' => 'Shipment placed on shelf',
            ],
            self::WMS_SHELVE_ASSIGNED => [
                'label' => 'Shelve Assigned',
                'icon' => 'layers',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment assigned to a shelf',
            ],
            self::WMS_BIN_ASSIGNED => [
                'label' => 'Bin Assigned',
                'icon' => 'package',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment assigned to a bin',
            ],
            self::WMS_PICKED => [
                'label' => 'Picked (WMS)',
                'icon' => 'shopping-bag',
                'color_bg' => '#e0f7fa',
                'color_text' => '#006064',
                'description' => 'Shipment picked from warehouse',
            ],
            self::WMS_OUTBOUND => [
                'label' => 'Outbound (WMS)',
                'icon' => 'log-out',
                'color_bg' => '#fff3e0',
                'color_text' => '#e65100',
                'description' => 'Shipment marked for outbound',
            ],
            self::RETRIEVED => [
                'label' => 'Retrieved',
                'icon' => 'arrow-up-circle',
                'color_bg' => '#e1f5fe',
                'color_text' => '#0277bd',
                'description' => 'Shipment retrieved for dispatch',
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
            self::OUT_FOR_DELIVERY => [
                'label' => 'Out for Delivery',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment is out with courier',
            ],
            self::DELIVERED => [
                'label' => 'Delivered',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully delivered',
            ],
            self::ATTEMPTED => [
                'label' => 'Attempted',
                'icon' => 'alert-circle',
                'color_bg' => '#fff4e5',
                'color_text' => '#b36b00',
                'description' => 'Delivery attempt made',
            ],
            self::ATTEMPTED_WRONG_ADDRESS => [
                'label' => 'Attempted - Wrong Address',
                'icon' => 'map-pin',
                'color_bg' => '#fffde7',
                'color_text' => '#f57f17',
                'description' => 'Attempted delivery, wrong address provided',
            ],
            self::ATTEMPTED_UNREACHABLE => [
                'label' => 'Attempted - Unreachable',
                'icon' => 'phone-off',
                'color_bg' => '#fce4ec',
                'color_text' => '#c2185b',
                'description' => 'Attempted delivery, recipient unreachable',
            ],
            self::ATTEMPTED_UNAVAILABLE => [
                'label' => 'Attempted - Unavailable',
                'icon' => 'user-x',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Attempted delivery, recipient unavailable',
            ],
            self::REFUSED => [
                'label' => 'Refused',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Recipient refused the shipment',
            ],
            self::REFUSED_WRONG_PACKAGE => [
                'label' => 'Refused - Wrong Package',
                'icon' => 'package-x',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Refused delivery due to wrong package',
            ],
            self::REFUSED_DELAYED => [
                'label' => 'Refused - Delayed',
                'icon' => 'clock',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Refused delivery due to delay',
            ],
            self::REFUSED_DAMAGED => [
                'label' => 'Refused - Damaged',
                'icon' => 'alert-octagon',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Refused delivery due to damage',
            ],
            self::DEBRIEF_OUTSTANDING => [
                'label' => 'Outstanding',
                'icon' => 'alert-triangle',
                'color_bg' => '#ffebee',   // light red background
                'color_text' => '#c62828', // deep red text
                'description' => 'Pending debrief review',
            ],
            self::CANCELLED => [
                'label' => 'Cancelled',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Shipment cancelled',
            ],
            self::HOLD => [
                'label' => 'On Hold',
                'icon' => 'pause-circle',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment placed on hold',
            ],

            self::HOLD_PAYMENT_ISSUE => [
                'label' => 'On Hold - Payment Issue',
                'icon' => 'credit-card',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment on hold due to payment issue',
            ],
            self::HOLD_DOCUMENTS => [
                'label' => 'On Hold - Missing Documents',
                'icon' => 'file-text',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment on hold pending documents',
            ],
            self::HOLD_CUSTOMS => [
                'label' => 'On Hold - Customs',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment on hold for customs clearance',
            ],
            self::CUSTOMS_HOLD => [
                'label' => 'Customs Hold',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment held in customs',
            ],
            self::DAMAGED => [
                'label' => 'Damaged',
                'icon' => 'alert-octagon',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment damaged',
            ],
            self::STOLEN => [
                'label' => 'Stolen',
                'icon' => 'lock',
                'color_bg' => '#fbe9e7',
                'color_text' => '#bf360c',
                'description' => 'Shipment reported stolen',
            ],
            self::LOST => [
                'label' => 'Lost',
                'icon' => 'help-circle',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment lost in transit',
            ],
            self::WEATHER => [
                'label' => 'Weather Delay',
                'icon' => 'cloud-rain',
                'color_bg' => '#e1f5fe',
                'color_text' => '#0277bd',
                'description' => 'Shipment delayed due to weather',
            ],
            self::SECURITY => [
                'label' => 'Security Issue',
                'icon' => 'shield-alert',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment held for security reasons',
            ],
            self::OVERAGE => [
                'label' => 'Overage',
                'icon' => 'plus-circle',
                'color_bg' => '#fbe9e7',
                'color_text' => '#bf360c',
                'description' => 'Extra shipment found',
            ],


            self::CANCELLED_CUSTOMER_REQUEST => [
                'label' => 'Cancelled - Customer Request',
                'icon' => 'user-x',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment cancelled upon customer request',
            ],
            self::CANCELLED_OPERATIONAL_ISSUE => [
                'label' => 'Cancelled - Operational Issue',
                'icon' => 'alert-triangle',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment cancelled due to operational issue',
            ],
            self::MISSING_SHELVE => [
                'label' => 'Missing',
                'icon' => 'help-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment missing from shelf',
            ],
            self::WMS_MISSING_SHELVE => [
                'label' => 'WMS Missing',
                'icon' => 'alert-triangle',
                'color_bg' => '#fff8e1',
                'color_text' => '#ef6c00',
                'description' => 'Shipment missing in WMS',
            ],
            self::CLOSED => [
                'label' => 'Closed',
                'icon' => 'lock',
                'color_bg' => '#eeeeee',
                'color_text' => '#424242',
                'description' => 'Shipment closed',
            ],
            self::OPERATIONS_QC => [
                'label' => 'Operations QC',
                'icon' => 'check',
                'color_bg' => '#e8f5e9',
                'color_text' => '#1b5e20',
                'description' => 'Quality check completed',
            ],
            self::ARRIVED => [
                'label' => 'Arrived',
                'icon' => 'map-pin',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Shipment has arrived at the destination hub',
            ],
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
                'description' => 'Shipment created in system',
            ],
            self::PICKED_UP => [
                'label' => 'Picked Up',
                'icon' => 'truck',
                'color_bg' => '#e6f7ff',
                'color_text' => '#007bff',
                'description' => 'Shipment picked up',
            ],

            self::RECEIVED_OPERATION => [
                'label' => 'Received in operation',
                'icon' => 'check-circle',
                'color_bg' => '#c8e6c9',
                'color_text' => '#000000',
                'description' => 'AWB has been scanned successfully'
            ],
            self::SHELVED => [
                'label' => 'Shelved',
                'icon' => 'archive',
                'color_bg' => '#fffde7',
                'color_text' => '#f57f17',
                'description' => 'Shipment placed on shelf',
            ],
            self::WMS_SHELVE_ASSIGNED => [
                'label' => 'Shelve Assigned',
                'icon' => 'layers',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment assigned to a shelf',
            ],
            self::WMS_BIN_ASSIGNED => [
                'label' => 'Bin Assigned',
                'icon' => 'package',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment assigned to a bin',
            ],
            self::WMS_PICKED => [
                'label' => 'Picked (WMS)',
                'icon' => 'shopping-bag',
                'color_bg' => '#e0f7fa',
                'color_text' => '#006064',
                'description' => 'Shipment picked from warehouse',
            ],
            self::WMS_OUTBOUND => [
                'label' => 'Outbound (WMS)',
                'icon' => 'log-out',
                'color_bg' => '#fff3e0',
                'color_text' => '#e65100',
                'description' => 'Shipment marked for outbound',
            ],
            self::RETRIEVED => [
                'label' => 'Retrieved',
                'icon' => 'arrow-up-circle',
                'color_bg' => '#e1f5fe',
                'color_text' => '#0277bd',
                'description' => 'Shipment retrieved for dispatch',
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
            self::OUT_FOR_DELIVERY => [
                'label' => 'Pending Scan',
                'icon' => 'fa-hourglass-half',
                'color_bg' => '#9e9e9e',
                'color_text' => '#ffffff',
                'description' => 'MAWB is out'
            ],
            self::DELIVERED => [
                'label' => 'Delivered',
                'icon' => 'check-circle',
                'color_bg' => '#e8f5e9',
                'color_text' => '#2e7d32',
                'description' => 'Shipment successfully delivered',
            ],
            self::ATTEMPTED => [
                'label' => 'Attempted',
                'icon' => 'alert-circle',
                'color_bg' => '#fff4e5',
                'color_text' => '#b36b00',
                'description' => 'Delivery attempt made',
            ],
            self::ATTEMPTED_WRONG_ADDRESS => [
                'label' => 'Attempted - Wrong Address',
                'icon' => 'map-pin',
                'color_bg' => '#fffde7',
                'color_text' => '#f57f17',
                'description' => 'Attempted delivery, wrong address provided',
            ],
            self::ATTEMPTED_UNREACHABLE => [
                'label' => 'Attempted - Unreachable',
                'icon' => 'phone-off',
                'color_bg' => '#fce4ec',
                'color_text' => '#c2185b',
                'description' => 'Attempted delivery, recipient unreachable',
            ],
            self::ATTEMPTED_UNAVAILABLE => [
                'label' => 'Attempted - Unavailable',
                'icon' => 'user-x',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Attempted delivery, recipient unavailable',
            ],
            self::REFUSED => [
                'label' => 'Refused',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Recipient refused the shipment',
            ],
            self::REFUSED_WRONG_PACKAGE => [
                'label' => 'Refused - Wrong Package',
                'icon' => 'package-x',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Refused delivery due to wrong package',
            ],
            self::REFUSED_DELAYED => [
                'label' => 'Refused - Delayed',
                'icon' => 'clock',
                'color_bg' => '#e3f2fd',
                'color_text' => '#1565c0',
                'description' => 'Refused delivery due to delay',
            ],
            self::REFUSED_DAMAGED => [
                'label' => 'Refused - Damaged',
                'icon' => 'alert-octagon',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Refused delivery due to damage',
            ],
            self::DEBRIEF_OUTSTANDING => [
                'label' => 'Outstanding',
                'icon' => 'alert-triangle',
                'color_bg' => '#ffebee',   // light red background
                'color_text' => '#c62828', // deep red text
                'description' => 'Pending debrief review',
            ],
            self::CANCELLED => [
                'label' => 'Cancelled',
                'icon' => 'x-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#b71c1c',
                'description' => 'Shipment cancelled',
            ],
            self::HOLD => [
                'label' => 'On Hold',
                'icon' => 'pause-circle',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment placed on hold',
            ],

            self::HOLD_PAYMENT_ISSUE => [
                'label' => 'On Hold - Payment Issue',
                'icon' => 'credit-card',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment on hold due to payment issue',
            ],
            self::HOLD_DOCUMENTS => [
                'label' => 'On Hold - Missing Documents',
                'icon' => 'file-text',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment on hold pending documents',
            ],
            self::HOLD_CUSTOMS => [
                'label' => 'On Hold - Customs',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment on hold for customs clearance',
            ],
            self::CUSTOMS_HOLD => [
                'label' => 'Customs Hold',
                'icon' => 'shield',
                'color_bg' => '#ede7f6',
                'color_text' => '#4527a0',
                'description' => 'Shipment held in customs',
            ],
            self::DAMAGED => [
                'label' => 'Damaged',
                'icon' => 'alert-octagon',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment damaged',
            ],
            self::STOLEN => [
                'label' => 'Stolen',
                'icon' => 'lock',
                'color_bg' => '#fbe9e7',
                'color_text' => '#bf360c',
                'description' => 'Shipment reported stolen',
            ],
            self::LOST => [
                'label' => 'Lost',
                'icon' => 'help-circle',
                'color_bg' => '#f3e5f5',
                'color_text' => '#6a1b9a',
                'description' => 'Shipment lost in transit',
            ],
            self::WEATHER => [
                'label' => 'Weather Delay',
                'icon' => 'cloud-rain',
                'color_bg' => '#e1f5fe',
                'color_text' => '#0277bd',
                'description' => 'Shipment delayed due to weather',
            ],
            self::SECURITY => [
                'label' => 'Security Issue',
                'icon' => 'shield-alert',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment held for security reasons',
            ],
            self::OVERAGE => [
                'label' => 'Overage',
                'icon' => 'plus-circle',
                'color_bg' => '#fbe9e7',
                'color_text' => '#bf360c',
                'description' => 'Extra shipment found',
            ],

            self::CANCELLED_CUSTOMER_REQUEST => [
                'label' => 'Cancelled - Customer Request',
                'icon' => 'user-x',
                'color_bg' => '#fff3e0',
                'color_text' => '#ef6c00',
                'description' => 'Shipment cancelled upon customer request',
            ],
            self::CANCELLED_OPERATIONAL_ISSUE => [
                'label' => 'Cancelled - Operational Issue',
                'icon' => 'alert-triangle',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment cancelled due to operational issue',
            ],
            self::MISSING_SHELVE => [
                'label' => 'Missing',
                'icon' => 'help-circle',
                'color_bg' => '#ffebee',
                'color_text' => '#c62828',
                'description' => 'Shipment missing from shelf',
            ],
            self::WMS_MISSING_SHELVE => [
                'label' => 'WMS Missing',
                'icon' => 'alert-triangle',
                'color_bg' => '#fff8e1',
                'color_text' => '#ef6c00',
                'description' => 'Shipment missing in WMS',
            ],
            self::CLOSED => [
                'label' => 'Closed',
                'icon' => 'lock',
                'color_bg' => '#eeeeee',
                'color_text' => '#424242',
                'description' => 'Shipment closed',
            ],
            self::OPERATIONS_QC => [
                'label' => 'Operations QC',
                'icon' => 'check',
                'color_bg' => '#e8f5e9',
                'color_text' => '#1b5e20',
                'description' => 'Quality check completed',
            ],

            self::ARRIVED => [
                'label' => 'Received in hub',
                'icon' => 'check-circle',
                'color_bg' => '#c8e6c9',
                'color_text' => '#000000',
                'description' => 'AWB has been scanned successfully'
            ],
        };
    }
}