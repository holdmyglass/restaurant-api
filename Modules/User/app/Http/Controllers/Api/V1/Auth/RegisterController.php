<?php

namespace Modules\User\Http\Controllers\Api\V1\Auth;

use Illuminate\Http\JsonResponse;
use Modules\Shared\Enums\ServerStatusCodeEnum;
use Modules\Shared\Helpers\ApiResponse;
use Modules\Shared\Http\Controllers\Controller;
use Modules\User\Http\Requests\V1\Auth\AccountVerifyRequest;
use Modules\User\Http\Requests\V1\Auth\RegisterRequest;
use Modules\User\Services\V1\AuthService;

final class RegisterController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $res = $this->authService->register($request);

        $response = ApiResponse::success($res['data'], $res['message'], $res['status'], $res['status_code']);

        return $response->toJson();

        try {

            $res = $this->authService->register($request);

            $response = ApiResponse::success($res['data'], $res['message'], $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }

    public function verifyAccount(AccountVerifyRequest $request): JsonResponse
    {

        try {

            $res = $this->authService->verifyAccount($request);

            $response = ApiResponse::success($res['data'], $res['message'], $res['status'], $res['status_code']);

            return $response->toJson();

        } catch (\Throwable $th) {

            $response = ApiResponse::error(__('shared::messages.error.something_went_wrong'), 'error', ServerStatusCodeEnum::INTERNAL_SERVER_ERROR);

            return $response->toJson();
        }
    }
}
