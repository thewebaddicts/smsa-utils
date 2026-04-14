<?php

namespace twa\smsautils\Enums;

enum AttributeTypeEnum: string
{
    case TOGGLE = 'toggle';
    case TEXTFIELD = 'textfield';
    case DROPDOWN = 'dropdown';

public static function info($value)
{
    return match ($value) {
        self::TOGGLE => [
            'label' => 'Toggle',
            'value' => self::TOGGLE,
        ],
        self::TEXTFIELD => [
            'label' => 'Textfield',
            'value' => self::TEXTFIELD,
        ],
        self::DROPDOWN => [
            'label' => 'Dropdown',
            'value' => self::DROPDOWN,
        ],
        
    };
}
} 