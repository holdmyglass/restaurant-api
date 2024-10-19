<?php

namespace Modules\Product\Enums;

enum ProductCategoryTypeEnum: string
{
    case DISH = 'DISH'; // For restaurants

    /**
     * Get the default category type
     */
    public static function default(): self
    {
        return self::DISH;
    }
}
