<?php

namespace Modules\User\Events\V1;

use Illuminate\Support\Facades\Event;
use Modules\Shared\Services\V1\AppService;
use Modules\User\Models\User;

class EmailVerified extends Event
{
    public function __construct(public User $user, public AppService $appService) {}
}
