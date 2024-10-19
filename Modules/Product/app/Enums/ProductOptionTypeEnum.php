<?php

namespace Modules\Product\Enums;

enum ProductOptionTypeEnum: string
{
    case OPTION = 'OPTION';
    case SUPPLEMENT = 'SUPPLEMENT';

    /**
     * Get the default option type.
     */
    public static function default(): self
    {
        return self::OPTION;
    }
}
