<?php

namespace Modules\User\Repositories\V1;

use Illuminate\Support\Facades\Auth;
use Modules\User\DTO\V1\ProfileDTO;
use Modules\User\DTO\V1\ProfileImageDTO;
use Modules\User\Interfaces\V1\ProfileRepositoryInterface;
use Modules\User\Models\Profile;

class ProfileRepository implements ProfileRepositoryInterface
{
    public function createProfile(ProfileDTO $profileDto): Profile
    {
        $profile = new Profile;
        $profile->user_id = Auth::id() ?? $profileDto->userId;
        $profile->type = $profileDto->type;
        $profile->full_name = $profileDto->fullName ?? null;
        $profile->description = $profileDto->description ?? null;
        $profile->email = $profileDto->email ?? null;
        $profile->phone = $profileDto->phone ?? null;

        $profile->save();

        return $profile;
    }

    public function updateProfile(ProfileDTO $profileDto, Profile $profile): Profile
    {

        $profile->user_id = Auth::id() ?? $profileDto->userId;
        $profile->type = $profileDto->type;
        $profile->full_name = $profileDto->fullName ?? null;
        $profile->description = $profileDto->description ?? null;
        $profile->email = $profileDto->email ?? null;
        $profile->phone = $profileDto->phone ?? null;

        $profile->save();

        return $profile;
    }

    public function updateDisplayImage(Profile $profile, ProfileImageDTO $profileImageDto): Profile
    {

        $profile->display_img = $profileImageDto->displayImg;
        $profile->save();

        return $profile;
    }

    public function updateCoverImage(Profile $profile, ProfileImageDTO $profileImageDto): Profile
    {

        $profile->cover_img = $profileImageDto->coverImg;
        $profile->save();

        return $profile;
    }
}
