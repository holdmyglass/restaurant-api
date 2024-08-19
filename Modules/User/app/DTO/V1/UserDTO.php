<?php

namespace Modules\User\DTO\V1;

readonly class UserDTO
{
    public function __construct(
        public ?string $email,
        public ?string $phone,
        public string $password
    ) {}
}
