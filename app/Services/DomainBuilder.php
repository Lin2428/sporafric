<?php

namespace App\Services;

use App\Models\SynchronizeParameter;

class DomainBuilder
{
    public static function build(string $model): array
    {
        $filters = SynchronizeParameter::with('operatorFilter')
            ->where('model', $model)
            ->where('is_active', 1)
            ->get();

        $domain = [];

        foreach ($filters as $filter) {

            $operator = $filter->operatorFilter->operator;
            $value = $filter->value;

            // fallback si ce n’est pas du JSON
            if (is_null($value)) {
                $value = $filter->value;
            }

            // sécurisation opérateur
            if (!self::isValidOperator($operator)) {
                continue;
            }

            // typage dynamique
            $value = self::castValue($value, $filter->field_type);

            $domain[] = ([
                $filter->field,
                $operator,
                $value
            ]);
        }

        return $domain;
    }

    private static function isValidOperator(string $operator): bool
    {
        return in_array($operator, [
            '=',
            '!=',
            '>',
            '<',
            '>=',
            '<=',
            'in',
            'not in',
            'like',
            'ilike',
            'not like',
            'not ilike'
        ]);
    }

    private static function castValue($value, string $type)
    {
        switch ($type) {

            case 'int':
                if (is_array($value)) {
                    return array_map('intval', $value);
                }
                return intval($value);

            case 'float':
                if (is_array($value)) {
                    return array_map('floatval', $value);
                }
                return floatval($value);

            case 'boolean':
                if (is_array($value)) {
                    return array_map(fn($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN), $value);
                }
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);

            case 'date':
                if (is_array($value)) {
                    return array_map(fn($v) => date('Y-m-d', strtotime($v)), $value);
                }
                return date('Y-m-d', strtotime($value));

            case 'datetime':
                if (is_array($value)) {
                    return array_map(fn($v) => date('Y-m-d H:i:s', strtotime($v)), $value);
                }
                return date('Y-m-d H:i:s', strtotime($value));

            case 'string':
            default:
                return $value;
        }
    }
}
