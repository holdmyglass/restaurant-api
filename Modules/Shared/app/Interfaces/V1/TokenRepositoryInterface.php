<?php

namespace Modules\Shared\Interfaces\V1;

use Illuminate\Database\Eloquent\Collection;
use Modules\Shared\DTO\V1\TokenDeleteDTO;
use Modules\Shared\DTO\V1\TokenDTO;
use Modules\Shared\Models\Token;

interface TokenRepositoryInterface
{
    public function createToken(TokenDTO $tokenDTO): Token;

    public function deleteToken(Token $token, ?string $deleted_by = null): bool;

    public function getTokensByScopeAndId(string $scope, int $id, string $type): Collection;

    public function deleteTokensByScopeAndId(TokenDeleteDTO $tokenDeleteDTO): void;

    public function tokenExist(string $tokenable_type, string $tokenable_id, string $token, ?string $scope = null): ?array;

    public function isTokenExpired(Token $token): bool;

    public function isTokenUsed(Token $token): bool;
}
