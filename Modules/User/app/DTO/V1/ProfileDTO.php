<?php

namespace Modules\User\DTO\V1;

readonly class ProfileDTO
{
    public function __construct(
        public ?string $userId,
        public string $type,
        public ?string $userName = null,
        public ?string $fullName = null,
        public ?string $description = null,
        public ?string $email = null,
        public ?string $phone = null,
    ) {}
}
