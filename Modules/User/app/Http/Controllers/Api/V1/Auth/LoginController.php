<?php

namespace Modules\User\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Shared\Enums\ServerStatusCodeEnum;
use Modules\Shared\Helpers\ApiResponse;
use Modules\Shared\Http\Controllers\Controller;
use Modules\User\Http\Requests\V1\Auth\LoginRequest;
use Modules\User\Services\V1\AuthService;

final class LoginController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {

        try {

            $res = $this->authService->login($request);

            $response = ApiResponse::success($res['data'], $res['message'], $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {

            $res = $this->authService->logout($request);

            $response = ApiResponse::success($res['data'], $res['message'], $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {
            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function logoutFromAllDevice(Request $request): JsonResponse
    {
        try {

            $res = $this->authService->logoutFromAllDevice($request);

            $response = ApiResponse::success($res['data'], $res['message'], $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {
            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }
}
