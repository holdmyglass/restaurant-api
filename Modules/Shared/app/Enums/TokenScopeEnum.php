<?php

namespace Modules\Shared\Enums;

enum TokenScopeEnum: string
{
    case VERIFY_EMAIL = 'verify_email';
    case VERIFY_PHONE = 'verify_phone';
    case RESET_PASSWORD_VIA_EMAIL = 'reset_password_via_email';
    case RESET_PASSWORD_VIA_PHONE = 'reset_password_via_phone';
}
