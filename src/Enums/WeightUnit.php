<?php

namespace twa\smsautils\Enums;

enum WeightUnit: string
{
    case G = 'g';
    case KG = 'kg';
    case LB = 'lb';
    case LBS = 'lbs';
    
    public function getRatioToKG(): float
    {
        return match($this) {
            self::G => 1/1000,
            self::KG => 1,
            self::LB => 453.592/1000
        };
    }
} 