<?php

namespace Modules\Product\Enums;

enum ProductLevelEnum: string
{
    case PRODUCT = 'PRODUCT'; // General main level product
    case OPTION = 'OPTION'; // colors, sauce etc
    case SUPPLEMENT = 'SUPPLEMENT'; // rice, noodles etc
    case SERVICE = 'SERVICE'; // useful for others than restaurant or specially service based exommerce

    /**
     * Get the default product level
     */
    public static function default(): self
    {
        return self::PRODUCT;
    }
}
