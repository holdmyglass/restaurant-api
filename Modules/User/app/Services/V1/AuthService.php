<?php

namespace Modules\User\Services\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Shared\Enums\ServerStatusCodeEnum;
use Modules\Shared\Enums\TokenableTypeEnum;
use Modules\Shared\Enums\TokenScopeEnum;
use Modules\Shared\Interfaces\V1\TokenRepositoryInterface;
use Modules\Shared\Services\V1\AppService;
use Modules\Shared\Services\V1\SystemService;
use Modules\Shared\Services\V1\TokenService;
use Modules\User\DTO\V1\ProfileDTO;
use Modules\User\DTO\V1\UserDTO;
use Modules\User\Enums\ProfileTypeEnum;
use Modules\User\Enums\RegisterOptionEnum;
use Modules\User\Events\V1\EmailVerificationCodeGenerated;
use Modules\User\Events\V1\EmailVerified;
use Modules\User\Events\V1\PhoneVerificationCodeGenerated;
use Modules\User\Http\Requests\V1\Auth\AccountVerifyRequest;
use Modules\User\Http\Requests\V1\Auth\LoginRequest;
use Modules\User\Http\Requests\V1\Auth\RegisterRequest;
use Modules\User\Interfaces\V1\AuthRepositoryInterface;
use Modules\User\Interfaces\V1\ProfileRepositoryInterface;
use Modules\User\Models\User;
use Modules\User\Transformers\V1\Profile\LoggedInProfileResource;

class AuthService
{
    public function __construct(
        private readonly AuthRepositoryInterface $authrepository,
        private readonly ProfileRepositoryInterface $profileRepository,
        private readonly ProfileService $profileService,
        private readonly TokenService $tokenService,
        private readonly AppService $appService,
        private readonly TokenRepositoryInterface $tokenRepository,
        private readonly SystemService $systemService
    ) {}

    public function register(RegisterRequest $request): array
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

        $token = match ($request->identity) {
            RegisterOptionEnum::EMAIL->value => $this->tokenService->generateEmailVerificationCode($user->id),
            RegisterOptionEnum::PHONE->value => $this->tokenService->generatePhoneVerificationCode($user->id),
        };

        $eventClass = match ($request->identity) {
            RegisterOptionEnum::EMAIL->value => EmailVerificationCodeGenerated::class,
            RegisterOptionEnum::PHONE->value => PhoneVerificationCodeGenerated::class,
        };

        dispatch(new $eventClass($user, $token, $this->appService));

        return [
            'status_code' => ServerStatusCodeEnum::CREATED,
            'status' => 'success',
            'message' => __('user::messages.register.success'),
            'data' => null,
        ];
    }

    public function verifyAccount(AccountVerifyRequest $request): array
    {
        if ($request->identity == RegisterOptionEnum::EMAIL->value) {
            return $this->verifyEmail($request);
        }

        if ($request->identity == RegisterOptionEnum::PHONE->value) {
            return $this->verifyPhone($request);
        }

        return [];

    }

    private function verifyEmail(AccountVerifyRequest $request): array
    {

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return
                [
                    'data' => null,
                    'status_code' => ServerStatusCodeEnum::NOT_FOUND,
                    'status' => 'error',
                    'message' => __('user::messages.verify.email_not_registered'),
                ];
        }

        if ($user->email_verified_at) {
            return [
                'data' => null,
                'status_code' => ServerStatusCodeEnum::OK->value,
                'status' => 'success',
                'message' => __('user::messages.verify.email_already_verified'),
            ];
        }

        $exist = $this->tokenRepository->tokenExist(TokenableTypeEnum::USER->value, $user->id, $request->token, TokenScopeEnum::VERIFY_EMAIL->value);

        if (! $exist) {
            return [
                'data' => null,
                'status_code' => ServerStatusCodeEnum::NOT_FOUND,
                'status' => 'error',
                'message' => __('shared::messages.request.400'),
            ];
        }

        $expired = $this->tokenRepository->isTokenExpired($exist['token']);

        if ($expired) {
            return [
                'data' => null,
                'status_code' => ServerStatusCodeEnum::FORBIDDEN,
                'status' => 'error',
                'message' => __('user::messages.verify.token_expired'),
            ];
        }

        $used = $this->tokenRepository->isTokenUsed($exist['token']);

        if ($used) {
            return [
                'data' => null,
                'status_code' => ServerStatusCodeEnum::FORBIDDEN,
                'status' => 'error',
                'message' => __('user::messages.verify.token_used'),
            ];
        }

        $user = $this->authrepository->verifyEmail($user);

        $this->tokenRepository->deleteToken($exist['token'], $this->systemService->getSystemId());

        event(new EmailVerified($user, $this->appService));

        return [
            'data' => null,
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
            'message' => __('user::messages.verify.email_verified'),
        ];

    }

    private function verifyPhone(AccountVerifyRequest $request): array
    {

        $user = User::where('email', $request->phone)->first();

        if (! $user) {
            return
                [
                    'data' => null,
                    'status_code' => ServerStatusCodeEnum::NOT_FOUND,
                    'status' => 'error',
                    'message' => __('user::messages.verify.phone_not_registered'),
                ];
        }

        if ($user->email_verified_at) {
            return [
                'data' => null,
                'status_code' => ServerStatusCodeEnum::OK,
                'status' => 'success',
                'message' => __('user::messages.verify.phone_already_verified'),
            ];
        }

        $exist = $this->tokenRepository->tokenExist(TokenableTypeEnum::USER->value, $user->id, $request->token, TokenScopeEnum::VERIFY_EMAIL->value);

        if (! $exist) {
            return [
                'data' => null,
                'status_code' => ServerStatusCodeEnum::NOT_FOUND,
                'status' => 'error',
                'message' => __('shared::messages.request.400'),
            ];
        }

        $used = $this->tokenRepository->isTokenUsed($exist['token']);

        if ($used) {
            return [
                'data' => null,
                'status_code' => ServerStatusCodeEnum::FORBIDDEN,
                'status' => 'error',
                'message' => __('user::messages.verify.token_used'),
            ];
        }

        $user = $this->authrepository->verifyPhone($user);

        $this->tokenRepository->deleteToken($exist['token'], $this->systemService->getSystemId());

        // event(new PhoneVerified($user, $this->appService));

        return [
            'data' => null,
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
            'message' => __('user::messages.verify.phone_verified'),
        ];

    }

    public function login(LoginRequest $request): array
    {
        if ($request->identity == RegisterOptionEnum::EMAIL->value) {
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials, $request->remember)) {

                /** @var User $user */
                $user = Auth::user();

                if (! $user->email_verified_at) {
                    return
                       [
                           'data' => null,
                           'status_code' => ServerStatusCodeEnum::FORBIDDEN,
                           'status' => 'error',
                           'message' => __('user::messages.login.email_not_verified'),
                       ];
                }

                $token = $this->profileService->assignTokenToUserAndProfile($user);

                $profile = $this->profileService->getAuthenticatedProfileFromToken($token->accessToken);

                return
                [
                    'data' => [
                        'profile' => new LoggedInProfileResource($profile, $token->accessToken),
                    ],
                    'status_code' => ServerStatusCodeEnum::OK,
                    'status' => 'success',
                    'message' => __('user::messages.login.success'),
                ];

            } else {
                return
                    [
                        'data' => null,
                        'status_code' => ServerStatusCodeEnum::UNAUTHORIZED,
                        'status' => 'error',
                        'message' => __('user::messages.login.invalid_credentials'),
                    ];
            }
        }

        if ($request->identity == RegisterOptionEnum::PHONE->value) {
            $credentials = $request->only('phone', 'password');
            if (Auth::attempt($credentials, $request->remember)) {

                /** @var User $user */
                $user = Auth::user();

                if (! $user->phone_verified_at) {
                    return
                       [
                           'data' => null,
                           'status_code' => ServerStatusCodeEnum::FORBIDDEN,
                           'status' => 'error',
                           'message' => __('user::messages.login.phone_not_verified'),
                       ];
                }

                $token = $this->profileService->assignTokenToUserAndProfile($user);

                $profile = $this->profileService->getAuthenticatedProfileFromToken($token);

                return
                [
                    'data' => [
                        'profile' => new LoggedInProfileResource($profile, $token->accessToken),
                    ],
                    'status_code' => ServerStatusCodeEnum::OK,
                    'status' => 'success',
                    'message' => __('user::messages.login.success'),
                ];

            } else {
                return
                    [
                        'data' => null,
                        'status_code' => ServerStatusCodeEnum::UNAUTHORIZED,
                        'status' => 'error',
                        'message' => __('user::messages.login.invalid_credentials'),
                    ];
            }
        }

        return [];
    }

    public function logout(Request $request): array
    {
        $user = Auth::user();
        $user->token()->revoke();

        return
        [
            'data' => null,
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
            'message' => __('user::messages.logout.success'),
        ];
    }

    public function logoutFromAllDevice(Request $request): array
    {
        $user = Auth::user();
        $user->tokens->each(function ($token, $key) {
            $token->revoke();
        });

        return
        [
            'data' => null,
            'status_code' => ServerStatusCodeEnum::OK,
            'status' => 'success',
            'message' => __('user::messages.logout.all_success'),
        ];
    }
}
