<?php

namespace Modules\Shared\Enums;

enum TokenTypeEnum: string
{
    case ACCESS_TOKEN = 'access_token';
    case ACCESS_CODE = 'access_code';

    case RESET_TOKEN = 'reset_token';
    case RESET_CODE = 'reset_code';

    case VERIFY_TOKEN = 'verify_token';
    case VERIFY_CODE = 'verify_code';
}
