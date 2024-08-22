<?php

namespace Modules\User\DTO\V1;

class VerifyAccountDTO
{
    public function __construct(
        public ?string $email = null,
        public ?string $phone = null,
    ) {}
}
