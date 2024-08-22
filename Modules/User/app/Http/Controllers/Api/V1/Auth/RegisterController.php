<?php

namespace Modules\User\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\JsonResponse;
use Modules\Shared\Helpers\ApiResponse;
use Modules\Shared\Http\Controllers\Controller;
use Modules\User\Http\Requests\V1\Auth\AccountVerifyRequest;
use Modules\User\Http\Requests\V1\Auth\RegisterRequest;
use Modules\User\Services\V1\AuthService;

final class RegisterController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {

        try {

            $this->authService->register($request);

            $response = ApiResponse::success(null, 'Registration successful', 200);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error('Something went wrong, try again', 500);

            return $response->toJson();
        }
    }

    public function verifyAccount(AccountVerifyRequest $request): JsonResponse
    {

        try {

            $res = $this->authService->verifyAccount($request);

            $response = ApiResponse::success(null, $res['message'], $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error('Something went wrong, try again', 500);

            return $response->toJson();
        }
    }
}
