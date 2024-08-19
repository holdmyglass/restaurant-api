<?php

namespace Modules\Shared\Services\V1;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Shared\Enums\TokenableTypeEnum;
use Modules\Shared\Enums\TokenScopeEnum;
use Modules\Shared\Enums\TokenTypeEnum;
use Modules\Shared\Models\Token;
use Modules\User\Models\User;

class TokenService
{
    public function __construct(
        private SystemService $systemService
    ) {}

    public function generateEmailVerificationCode(string $id): string
    {
        return $this->saveToken($id, TokenableTypeEnum::USER->value, TokenScopeEnum::VERIFY_EMAIL->value, TokenTypeEnum::VERIFY_CODE->value);
    }

    public function generateEmailVerificationLink(string $id): string
    {
        return $this->saveToken($id, TokenableTypeEnum::USER->value, TokenScopeEnum::VERIFY_EMAIL->value, TokenTypeEnum::VERIFY_TOKEN->value);
    }

    public function generatePhoneVerificationCode(string $id): string
    {
        return $this->saveToken($id, TokenableTypeEnum::USER->value, TokenScopeEnum::VERIFY_PHONE->value, TokenTypeEnum::VERIFY_CODE->value);
    }

    public function generatePasswordResetCodeViaEmail(string $id): string
    {
        return $this->saveToken($id, TokenableTypeEnum::USER->value, TokenScopeEnum::RESET_PASSWORD_VIA_EMAIL->value, TokenTypeEnum::VERIFY_CODE->value);
    }

    public function generatePasswordResetCodeViaPhone(string $id): string
    {
        return $this->saveToken($id, TokenableTypeEnum::USER->value, TokenScopeEnum::RESET_PASSWORD_VIA_PHONE->value, TokenTypeEnum::VERIFY_CODE->value);
    }

    public function regenerateToken(Token $token): Token
    {
        $tkn = $this->generateToken($token->type->value);

        // create new token
        $newToken = new Token;
        $newToken->scope = $token->scope;
        $newToken->type = $token->type;
        $newToken->token = Hash::make($tkn);
        $newToken->tokenable_id = $token->tokenable_id;
        $newToken->tokenable_type = $token->tokenable_type->value;
        $newToken->expires_at = Carbon::now()->addSeconds(intval(config('shared.token.validity.'.$token->type->value)));
        $newToken->created_by = $this->systemService->getSystemId();
        $newToken->updated_by = $this->systemService->getSystemId();
        $newToken->save();

        // delete old token
        $token->deleted_by = $this->systemService->getSystemId();
        $token->save();
        $token->delete();

        return $newToken;
    }

    private function saveToken(string $id, string $type, string $scope, string $format): string
    {
        // delete all the tokens that are generated for this context before
        $exist_tokens = Token::where('scope', $scope)->where('tokenable_id', $id)->where('tokenable_type', $type)->get();
        if ($exist_tokens) {
            foreach ($exist_tokens as $tkn) {
                $tkn->deleted_by = $this->systemService->getSystemId();
                $tkn->save();
                $tkn->delete();
            }
        }

        $tkn = $this->generateToken($format);

        $token = new Token;
        $token->scope = $scope;
        $token->type = $format;
        $token->token = Hash::make($tkn);
        $token->tokenable_id = $id;
        $token->tokenable_type = $type;
        $token->expires_at = Carbon::now()->addSeconds(intval(config('shared.token.validity.'.$format)));
        $token->created_by = $this->systemService->getSystemId();
        $token->updated_by = $this->systemService->getSystemId();
        $token->save();

        return $tkn;
    }

    private function generateToken(string $format): string
    {
        switch ($format) {
            case TokenTypeEnum::ACCESS_TOKEN->value:
                $length = config('shared.token.length.'.TokenTypeEnum::ACCESS_TOKEN->value);
                $token = Str::random(intval($length));
                break;
            case TokenTypeEnum::VERIFY_TOKEN->value:
                $length = config('shared.token.length.'.TokenTypeEnum::VERIFY_TOKEN->value);
                $token = Str::random(intval($length));
                break;
            case TokenTypeEnum::RESET_TOKEN->value:
                $length = config('shared.token.length.'.TokenTypeEnum::RESET_TOKEN->value);
                $token = Str::random(intval($length));
                break;
            case TokenTypeEnum::ACCESS_CODE->value:
                $length = config('shared.token.length.'.TokenTypeEnum::ACCESS_CODE->value);
                $highestNumber = pow(10, $length) - 1;
                $token = mt_rand(0, $highestNumber);
                $token = $this->addLeadingZeros($token, $length);
                break;
            case TokenTypeEnum::VERIFY_CODE->value:
                $length = config('shared.token.length.'.TokenTypeEnum::VERIFY_CODE->value);
                $highestNumber = pow(10, $length) - 1;
                $token = mt_rand(0, $highestNumber);
                $token = $this->addLeadingZeros($token, $length);
                break;
            case TokenTypeEnum::RESET_CODE->value:
                $length = config('shared.token.length.'.TokenTypeEnum::RESET_CODE->value);
                $highestNumber = pow(10, $length) - 1;
                $token = mt_rand(0, $highestNumber);
                $token = $this->addLeadingZeros($token, $length);
                break;
            default:
                $token = Str::random(60);
        }

        return $token;
    }

    public function doesTokenExist(string $token, string $tokenableId, string $tokenableType, string $scope, string $type): ?array
    {

        $holder = User::find($tokenableId);
        if (! $holder) {
            return null;
        }

        $tkns = Token::where('tokenable_id', $tokenableId)->where('tokenable_type', $tokenableType)->where('scope', $scope)->where('type', $type)->get();
        if (! $tkns) {
            return null;
        }

        $valid_token = null;

        foreach ($tkns as $tkn) {
            if (Hash::check($token, $tkn->token)) {
                $valid_token = $tkn;
                break;
            }
        }

        if (! $valid_token) {
            return null;
        }

        return [
            'user' => $holder,
            'token' => $valid_token,
        ];
    }

    public function isTokenExpired(Token $token): bool
    {
        return $token->expires_at->isPast();
    }

    public function isTokenUsed(Token $token): bool
    {
        return (bool) $token->used_at;
    }

    public function encodeToken(array $content): string
    {
        return base64_encode(json_encode($content));
    }

    public function decodeToken(string $token): array
    {
        return json_decode(base64_decode($token), true);
    }

    /**
     * Add leading zeros to a number to match the number of digits
     * can be used to generate verification codes
     */
    private function addLeadingZeros($number, $desiredDigits)
    {
        // Convert the number to a string
        $numberStr = (string) $number;

        // Calculate the number of zeros to add
        $zerosToAdd = max(0, $desiredDigits - strlen($numberStr));

        // Add leading zeros
        $result = str_repeat('0', $zerosToAdd).$numberStr;

        return $result;
    }
}
