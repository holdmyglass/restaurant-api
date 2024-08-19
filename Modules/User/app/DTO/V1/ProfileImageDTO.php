<?php

namespace Modules\User\DTO\V1;

readonly class ProfileImageDTO
{
    public function __construct(
        public string $profileId,
        public ?string $displayImg = null,
        public ?string $coverImg = null,
    ) {}
}
