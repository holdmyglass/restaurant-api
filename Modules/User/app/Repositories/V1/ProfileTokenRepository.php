<?php

namespace Modules\User\Repositories\V1;

use Modules\User\DTO\V1\ProfileTokenDTO;
use Modules\User\Interfaces\V1\ProfileTokenRepositoryInterface;
use Modules\User\Models\ProfileToken;

class ProfileTokenRepository implements ProfileTokenRepositoryInterface
{
    public function createProfileToken(ProfileTokenDTO $profileTokenDto): ProfileToken
    {

        $profileToken = new ProfileToken;
        $profileToken->profile_id = $profileTokenDto->profileId;
        $profileToken->access_token = $profileTokenDto->accessToken;
        $profileToken->save();

        return $profileToken;
    }
}
