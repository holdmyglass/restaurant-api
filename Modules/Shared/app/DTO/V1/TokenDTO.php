<?php

namespace Modules\Shared\DTO\V1;

use Carbon\Carbon;

class TokenDTO
{
    public function __construct(
        public string $scope,
        public string $format,
        public string $token,
        public string $tokenableId,
        public string $tokenableType,
        public ?Carbon $expiresAt = null,
        public ?string $createdBy = null,
        public ?string $updatedBy = null
    ) {}
}
