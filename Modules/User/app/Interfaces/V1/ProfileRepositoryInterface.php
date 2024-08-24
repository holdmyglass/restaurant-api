<?php

namespace Modules\User\Interfaces\V1;

use Modules\User\DTO\V1\ProfileDTO;
use Modules\User\DTO\V1\ProfileImageDTO;
use Modules\User\Models\Profile;

interface ProfileRepositoryInterface
{
    public function createProfile(ProfileDTO $profileDTO): Profile;

    public function updateDisplayImage(Profile $profile, ProfileImageDTO $profileImageDto): Profile;

    public function updateCoverImage(Profile $profile, ProfileImageDTO $profileImageDto): Profile;
}
