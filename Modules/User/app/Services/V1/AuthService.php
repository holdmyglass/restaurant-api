<?php

namespace Modules\User\Services\V1;

use Modules\Shared\Services\V1\AppService;
use Modules\Shared\Services\V1\TokenService;
use Modules\User\DTO\V1\ProfileDTO;
use Modules\User\DTO\V1\UserDTO;
use Modules\User\Enums\ProfileTypeEnum;
use Modules\User\Events\V1\EmailVerificationCodeGenerated;
use Modules\User\Events\V1\PhoneVerificationCodeGenerated;
use Modules\User\Http\Requests\V1\Auth\RegisterRequest;
use Modules\User\Interfaces\V1\AuthRepositoryInterface;
use Modules\User\Interfaces\V1\ProfileRepositoryInterface;
use Modules\User\Models\User;

class AuthService
{
    public function __construct(
        private readonly AuthRepositoryInterface $authrepository,
        private readonly ProfileRepositoryInterface $profileRepository,
        private readonly ProfileService $profileService,
        private readonly TokenService $tokenService,
        private readonly AppService $appService
    ) {}

    public function register(RegisterRequest $request): User
    {

        $userDTO = new UserDTO(
            email: $request->email,
            phone: $request->phone,
            password: $request->password
        );

        $user = $this->authrepository->createuser($userDTO);

        $profileDTO = new ProfileDTO(
            userId: $user->id,
            type: ProfileTypeEnum::PRIMARY->value,
            userName: $request->user_name ? $this->profileService->generateUsername($request->user_name) : $this->profileService->generateUsername($request->full_name),
            fullName: $request->full_name ?? null,
            description: $request->description ?? null,
            email: $request->email ?? null,
            phone: $request->phone ?? null
        );

        $profile = $this->profileRepository->createProfile($profileDTO);

        if ($profile && $request->displayImg) {
            $this->profileService->saveDisplayImage($request->displayImg, $profile);
        }
        if ($profile && $request->coverImg) {
            $this->profileService->saveCoverImage($request->coverImg, $profile);
        }

        if ($request->identity == 'EMAIL') {
            $token = $this->tokenService->generateEmailVerificationCode($user->id);
            event(new EmailVerificationCodeGenerated($user, $token, $this->appService));
        }
        if ($request->identity == 'PHONE') {
            $token = $this->tokenService->generatePhoneVerificationCode($user->id);
            event(new PhoneVerificationCodeGenerated($user, $token, $this->appService));
        }

        return $user;
    }
}
