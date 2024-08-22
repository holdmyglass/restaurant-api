<?php

namespace Modules\Shared\Repositories\V1;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Modules\Shared\DTO\V1\TokenDeleteDTO;
use Modules\Shared\DTO\V1\TokenDTO;
use Modules\Shared\Interfaces\V1\TokenRepositoryInterface;
use Modules\Shared\Models\Token;

class TokenRepository implements TokenRepositoryInterface
{
    public function createToken(TokenDTO $tokenDTO): Token
    {

        $token = new Token;
        $token->scope = $tokenDTO->scope;
        $token->type = $tokenDTO->format;
        $token->token = Hash::make($tokenDTO->token);
        $token->tokenable_id = $tokenDTO->tokenableId;
        $token->tokenable_type = $tokenDTO->tokenableType;
        $token->expires_at = $tokenDTO->expiresAt;
        $token->created_by = $tokenDTO->createdBy;
        $token->updated_by = $tokenDTO->updatedBy;
        $token->save();

        return $token;
    }

    public function deleteToken(Token $token, ?string $deleted_by = null): bool
    {
        if ($deleted_by) {
            $token->deleted_by = $deleted_by;
            $token->save();
        }

        return $token->delete();
    }

    public function getTokensByScopeAndId(string $scope, int $id, string $type): Collection
    {
        return Token::where('scope', $scope)->where('tokenable_id', $id)->where('tokenable_type', $type)->get();
    }

    public function deleteTokensByScopeAndId(TokenDeleteDTO $tokenDeleteDTO): void
    {
        $tokens = Token::where('scope', $tokenDeleteDTO->scope)
            ->where('tokenable_id', $tokenDeleteDTO->tokenableId)
            ->where('tokenable_type', $tokenDeleteDTO->tokenableType)
            ->get();

        foreach ($tokens as $token) {
            $token->deleted_by = $tokenDeleteDTO->deletedBy;
            $token->save();
            $this->deleteToken($token);
        }
    }

    public function tokenExist(string $tokenable_type, string $tokenable_id, string $token, ?string $scope = null): ?array
    {
        $tokenable = $tokenable_type::find($tokenable_id);
        if (! $tokenable) {
            return null;
        }

        $tokens = Token::where('tokenable_type', $tokenable_type)
            ->where('tokenable_id', $tokenable_id)
            ->when($scope, function ($query) use ($scope) {
                $query->where('scope', $scope);
            })
            ->get();

        foreach ($tokens as $tokenEntity) {
            if (Hash::check($token, $tokenEntity->token)) {
                return [
                    'user' => $tokenable,
                    'token' => $tokenEntity,
                ];
            }
        }

        return null;
    }

    public function isTokenExpired(Token $token): bool
    {
        $expiresAt = Carbon::parse($token->expires_at);

        return (bool) $expiresAt->isPast();
    }

    public function isTokenUsed(Token $token): bool
    {
        return (bool) $token->used_at;
    }
}
