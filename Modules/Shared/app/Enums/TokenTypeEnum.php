<?php

namespace Modules\Shared\Enums;

enum TokenTypeEnum: string
{
    case ACCESS_TOKEN = 'ACCESS_TOKEN';
    case ACCESS_CODE = 'ACCESS_CODE';

    case RESET_TOKEN = 'RESET_TOKEN';
    case RESET_CODE = 'RESET_CODE';

    case VERIFY_TOKEN = 'VERIFY_TOKEN';
    case VERIFY_CODE = 'VERIFY_CODE';
}
