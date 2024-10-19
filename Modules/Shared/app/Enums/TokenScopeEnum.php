<?php

namespace Modules\Shared\Enums;

enum TokenScopeEnum: string
{
    case VERIFY_EMAIL = 'VERIFY_EMAIL';
    case VERIFY_PHONE = 'VERIFY_PHONE';
    case RESET_PASSWORD_VIA_EMAIL = 'RESET_PASSWORD_VIA_EMAIL';
    case RESET_PASSWORD_VIA_PHONE = 'RESET_PASSWORD_VIA_PHONE';
}
