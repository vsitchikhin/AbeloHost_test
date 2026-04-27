<?php

declare(strict_types=1);

namespace App\Support;

class Sort
{
    private const DEFAULT_FIELD     = 'published_at';
    private const DEFAULT_DIRECTION = 'desc';

    /**
     * @param list<string> $allowedFields
     * @return array{field: string, direction: string}
     */
    public static function normalize(
        string $field,
        string $direction,
        array $allowedFields = ['published_at', 'views']
    ): array {
        $normalizedField     = in_array($field, $allowedFields, true) ? $field : self::DEFAULT_FIELD;
        $normalizedDirection = in_array($direction, ['asc', 'desc'], true) ? $direction : self::DEFAULT_DIRECTION;

        return [
            'field'     => $normalizedField,
            'direction' => $normalizedDirection,
        ];
    }
}
