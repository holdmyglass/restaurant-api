<?php

namespace Modules\Shared\Enums;

enum TokenableTypeEnum: string
{
    case USER = 'Modules\User\Models\User';
    case PROFILE = 'Modules\User\Model\Profile';
}
