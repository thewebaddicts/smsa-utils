<?php

namespace twa\smsautils\Services;

use twa\smsautils\Enums\OperationsEnum;


final class WorkflowEventConditionEvaluator
{
    public function evaluate(array $variables, array | null $conditions): bool
    {

        if (is_null($conditions) || (is_array($conditions) && count($conditions) === 0)) {
            return true;
        }

        foreach ($conditions as $rule) {
            if (!$this->evaluateRule($variables, $rule)) {
                return false;
            }
        }

        return true;
    }

    private function evaluateRule(array $variables, array $rule): bool
    {
        $field = $rule['field'];
        $operator = OperationsEnum::from($rule['operator']);
        $expected = $rule['value'] ?? null;
      
        $actual = data_get($variables, $field);

        return match ($operator) {
            OperationsEnum::EQUAL => $this->equal($actual, $expected),
            OperationsEnum::NOT_EQUAL => !$this->equal($actual, $expected),

            OperationsEnum::GREATER_THAN => $this->compareNumeric($actual, $expected, '>'),
            OperationsEnum::LESS_THAN => $this->compareNumeric($actual, $expected, '<'),
            OperationsEnum::GREATER_THAN_OR_EQUAL_TO => $this->compareNumeric($actual, $expected, '>='),
            OperationsEnum::LESS_THAN_OR_EQUAL_TO => $this->compareNumeric($actual, $expected, '<='),

            OperationsEnum::CONTAINS => $this->contains($actual, $expected),
            OperationsEnum::STARTS_WITH => $this->startsWith($actual, $expected),
            OperationsEnum::ENDS_WITH => $this->endsWith($actual, $expected),

            OperationsEnum::IS_IN => $this->isIn($actual, $expected),
            OperationsEnum::IS_NOT_IN => !$this->isIn($actual, $expected),

            OperationsEnum::IS_EMPTY => $this->isEmptyValue($actual),
            OperationsEnum::IS_NOT_EMPTY => !$this->isEmptyValue($actual),

            OperationsEnum::IS_TRUE => $this->toBool($actual) === true,
            OperationsEnum::IS_FALSE => $this->toBool($actual) === false,
        };
    }

    private function equal(mixed $actual, mixed $expected): bool
    {
        if (is_string($actual) && is_string($expected)) {
            return mb_strtoupper(trim($actual)) === mb_strtoupper(trim($expected));
        }

        if ($this->isNumericLike($actual) && $this->isNumericLike($expected)) {
            return (float) $actual === (float) $expected;
        }

        return $actual === $expected;
    }

    private function compareNumeric(mixed $actual, mixed $expected, string $op): bool
    {
        if (!$this->isNumericLike($actual) || !$this->isNumericLike($expected)) {
            return false;
        }

        $a = (float) $actual;
        $b = (float) $expected;

        return match ($op) {
            '>' => $a > $b,
            '<' => $a < $b,
            '>=' => $a >= $b,
            '<=' => $a <= $b,
            default => false,
        };
    }

    private function contains(mixed $actual, mixed $expected): bool
    {
        if (is_array($actual)) {
            return in_array($expected, $actual, true);
        }

        if (is_string($actual)) {
            $needle = is_string($expected) ? $expected : (string) $expected;
            return str_contains(mb_strtolower($actual), mb_strtolower($needle));
        }

        return false;
    }

    private function startsWith(mixed $actual, mixed $expected): bool
    {
        if (!is_string($actual)) {
            return false;
        }
        $needle = is_string($expected) ? $expected : (string) $expected;
        return str_starts_with(mb_strtolower($actual), mb_strtolower($needle));
    }

    private function endsWith(mixed $actual, mixed $expected): bool
    {
        if (!is_string($actual)) {
            return false;
        }
        $needle = is_string($expected) ? $expected : (string) $expected;
        return str_ends_with(mb_strtolower($actual), mb_strtolower($needle));
    }

    private function isIn(mixed $actual, mixed $expected): bool
    {
        $list = $expected;
        if (is_string($expected)) {
            $list = array_values(array_filter(array_map('trim', explode(',', $expected)), fn ($v) => $v !== ''));
        }

        if (!is_array($list)) {
            return false;
        }

        foreach ($list as $item) {
            if ($this->equal($actual, $item)) {
                return true;
            }
        }

        return false;
    }

    private function isEmptyValue(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }
        if (is_string($value)) {
            return trim($value) === '';
        }
        if (is_array($value)) {
            return count($value) === 0;
        }

        return false;
    }

    private function toBool(mixed $value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value)) {
            return $value == 1 ? true : ($value == 0 ? false : null);
        }
        if (is_string($value)) {
            $v = mb_strtolower(trim($value));
            return match ($v) {
                '1', 'true' => true,
                '0', 'false' => false,
                default => null,
            };
        }

        return null;
    }

    private function isNumericLike(mixed $value): bool
    {
        return is_int($value) || is_float($value) || (is_string($value) && is_numeric(trim($value)));
    }
}

