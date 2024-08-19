<?php

namespace Modules\User\Interfaces\V1;

use Modules\User\DTO\V1\ProfileDTO;
use Modules\User\Models\Profile;

interface ProfileRepositoryInterface
{
    public function createProfile(ProfileDTO $profileDTO): Profile;
}
