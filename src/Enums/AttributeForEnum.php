<?php

namespace twa\smsautils\Enums;

enum AttributeForEnum: string
{ //label and value
    case ADDRESS = 'address';
    case MAWB_MANIFEST = 'mawb_manifest';
    case SHIPMENT = 'shipment';
public static function info($value)
{ //label and value
    return match ($value) {
        self::ADDRESS => [
            'label' => 'Address',
            'value' => self::ADDRESS,
        ],
        self::MAWB_MANIFEST => [
            'label' => 'MAWB Manifest',
            'value' => self::MAWB_MANIFEST,
        ],
    self::SHIPMENT => [
            'label' => 'Shipment',
            'value' => self::SHIPMENT,
        ],
    };
}
} 