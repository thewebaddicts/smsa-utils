<?php

namespace twa\smsautils\Enums;

enum DimensionUnit: string
{
    case CM = 'cm';
    case M = 'm';
    case MM = 'mm';
    case IN = 'in';
    case FT = 'ft';

    public function getRatioToCM(): float
    {
        return match($this) {
            self::CM => 1,
            self::M => 100,
            self::MM => 0.1,
            self::IN => 2.54,
            self::FT => 30.48,
        };
    }
} 