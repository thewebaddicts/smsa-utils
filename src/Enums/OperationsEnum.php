<?php

namespace twa\smsautils\Enums;

enum OperationsEnum: string
{
    case EQUAL = 'equal';
    case GREATER_THAN = 'greater_than';
    case LESS_THAN = 'less_than';
    case GREATER_THAN_OR_EQUAL_TO = 'greater_than_or_equal_to';
    case LESS_THAN_OR_EQUAL_TO = 'less_than_or_equal_to';
    case NOT_EQUAL = 'not_equal';
    case CONTAINS = 'contains';

    case IS_IN = 'is_in';
    case IS_NOT_IN = 'is_not_in';

    case STARTS_WITH = 'starts_with';
    case ENDS_WITH = 'ends_with';
    case IS_EMPTY = 'is_empty';
    case IS_NOT_EMPTY = 'is_not_empty';
    case IS_TRUE = 'is_true';
    case IS_FALSE = 'is_false';


    public static function info($value)
    {
        return match ($value) {
            self::EQUAL => [
                'label' => 'Equal',
                'value' => self::EQUAL,
                'required_value' => true
            ],
            self::NOT_EQUAL => [
                'label' => 'Not Equal',
                'value' => self::NOT_EQUAL,
                'required_value' => true
            ],
            self::GREATER_THAN => [
                'label' => 'Greater Than',
                'value' => self::GREATER_THAN,
                'required_value' => true
            ],
            self::LESS_THAN => [
                'label' => 'Less Than',
                'value' => self::LESS_THAN,
                'required_value' => true
            ],
            self::GREATER_THAN_OR_EQUAL_TO => [
                'label' => 'Greater Than or Equal To',
                'value' => self::GREATER_THAN_OR_EQUAL_TO,
                'required_value' => true
            ],
            self::LESS_THAN_OR_EQUAL_TO => [
                'label' => 'Less Than or Equal To',
                'value' => self::LESS_THAN_OR_EQUAL_TO,
                'required_value' => true
            ],
            self::IS_IN => [
                'label' => 'Is In',
                'value' => self::IS_IN,
                'required_value' => true
            ],
            self::IS_NOT_IN => [
                'label' => 'Is Not In',
                'value' => self::IS_NOT_IN,
                'required_value' => true
            ],
            self::STARTS_WITH => [
                'label' => 'Starts With',
                'value' => self::STARTS_WITH,
                'required_value' => true
            ],
            self::ENDS_WITH => [
                'label' => 'Ends With',
                'value' => self::ENDS_WITH,
                'required_value' => true
            ],
            self::IS_EMPTY => [
                'label' => 'Is Empty',
                'value' => self::IS_EMPTY,
                'required_value' => false
            ],
            self::IS_NOT_EMPTY => [
                'label' => 'Is Not Empty',
                'value' => self::IS_NOT_EMPTY,
                'required_value' => false
            ],
            self::IS_TRUE => [
                'label' => 'Is True',
                'value' => self::IS_TRUE,
                'required_value' => false
            ],
            self::IS_FALSE => [
                'label' => 'Is False',
                'value' => self::IS_FALSE,
                'required_value' => false
            ],
        };
    }

    public static function forTypeText(): array
    {
        return [
            self::info(self::EQUAL),
            self::info(self::NOT_EQUAL),
            self::info(self::STARTS_WITH),
            self::info(self::ENDS_WITH),
            self::info(self::IS_EMPTY),
            self::info(self::IS_NOT_EMPTY),
        ];
    }



    public static function forTypeMultiSelect()
    {
        return [
            self::info(self::IS_IN),
            self::info(self::IS_NOT_IN),
        ];
    }

    public static function forTypeSelect()
    {
        return [
            self::info(self::EQUAL),
            self::info(self::NOT_EQUAL),
        ];
    }

    public static function forTypeNumber(): array
    {
        return [
            self::info(self::EQUAL),
            self::info(self::NOT_EQUAL),
            self::info(self::GREATER_THAN),
            self::info(self::LESS_THAN),
            self::info(self::GREATER_THAN_OR_EQUAL_TO),
            self::info(self::LESS_THAN_OR_EQUAL_TO),
        ];
    }

    public static function forTypeBoolean(): array
    {
        return [
            self::info(self::IS_TRUE),
            self::info(self::IS_FALSE),
        ];
    }
}
