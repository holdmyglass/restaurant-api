<?php

namespace Modules\Shared\DTO\V1;

class TokenDeleteDTO
{
    public function __construct(
        public string $scope,
        public string $tokenableId,
        public string $tokenableType,
        public string $deletedBy
    ) {}
}
