<?php

namespace twa\smsautils\Enums;

enum CommodityType: string
{
    case STANDARD = 'STD';
    case FRAGILE = 'FRG';
    case HAZARDOUS = 'HAZ';
    case PERISHABLE = 'PER';

    public function getDescription(): string
    {
        return match($this) {
            self::STANDARD => 'Standard',
            self::FRAGILE => 'Fragile',
            self::HAZARDOUS => 'Hazardous',
            self::PERISHABLE => 'Perishable',
        };
    }
} 