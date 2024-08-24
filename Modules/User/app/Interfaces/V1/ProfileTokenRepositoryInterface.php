<?php

namespace Modules\User\Interfaces\V1;

use Modules\User\DTO\V1\ProfileTokenDTO;
use Modules\User\Models\ProfileToken;

interface ProfileTokenRepositoryInterface
{
    public function createProfileToken(ProfileTokenDTO $profileTokenDto): ProfileToken;
}
