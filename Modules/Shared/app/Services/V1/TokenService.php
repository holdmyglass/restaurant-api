<?php

namespace Modules\Shared\Services\V1;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Modules\Shared\DTO\V1\TokenDeleteDTO;
use Modules\Shared\DTO\V1\TokenDTO;
use Modules\Shared\Enums\TokenableTypeEnum;
use Modules\Shared\Enums\TokenScopeEnum;
use Modules\Shared\Enums\TokenTypeEnum;
use Modules\Shared\Interfaces\V1\TokenRepositoryInterface;

class TokenService
{
    public function __construct(
        private readonly SystemService $systemService,
        private readonly TokenRepositoryInterface $tokenRepository
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

    private function saveToken(string $id, string $type, string $scope, string $format): string
    {
        $tokenDeleteDTO = new TokenDeleteDTO(
            scope: $scope,
            tokenableId: $id,
            tokenableType: $type,
            deletedBy: $this->systemService->getSystemId()
        );

        // Delete existing tokens for this scope and type
        $this->tokenRepository->deleteTokensByScopeAndId($tokenDeleteDTO);

        $tkn = $this->generateToken($format);

        $tokenDTO = new TokenDTO(
            scope: $scope,
            format: $format,
            token: $tkn,
            tokenableId: $id,
            tokenableType: $type,
            expiresAt: Carbon::now()->addSeconds(intval(config('shared.token.validity.'.$format))),
            createdBy: $this->systemService->getSystemId(),
            updatedBy: $this->systemService->getSystemId()
        );

        $this->tokenRepository->createToken($tokenDTO);

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
