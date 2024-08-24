<?php

namespace Modules\User\DTO\V1;

readonly class ProfileTokenDTO
{
    public function __construct(
        public string $profileId,
        public string $accessToken
    ) {}
}
