<?php

namespace Modules\Product\Enums;

enum PriceTypeEnum: string
{
    case REGULAR = 'REGULAR';
    case OFFER = 'OFFER';

    public static function isValid(string $value): bool
    {
        // Check if the value matches any of the enum cases
        return in_array($value, array_column(self::cases(), 'value'), true);
    }

    /**
     * Get the default Price type
     */
    public static function default(): self
    {
        return self::REGULAR;
    }
}
