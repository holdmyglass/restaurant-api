<?php

namespace Modules\User\Enums;

enum UserLevelEnum: string
{
    case SYSTEM = 'SYSTEM'; // controls system level actions: .g. migrations, cron jobs, system updates
    case SUPERADMIN = 'SUPERADMIN'; // this is the super  admin that controls the website system i.e. Developer
    case ADMIN = 'ADMIN'; // this is the admin that controls the website dashboard
    case USER = 'USER'; // the basic users

}
