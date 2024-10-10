<?php

namespace Modules\Shared\Helpers;

use Modules\Shared\Enums\ServerStatusCodeEnum;
use Modules\Shared\Response\ApiErrorResponse;
use Modules\Shared\Response\ApiSuccessResponse;
use Modules\Shared\Response\ApiValidationErrorResponse;

class ApiResponse
{
    public static function success(?array $data = null, ?string $message = null, string $status = 'success', ServerStatusCodeEnum $statusCode = ServerStatusCodeEnum::OK): ApiSuccessResponse
    {
        return new ApiSuccessResponse($data, $message, $status, $statusCode);
    }

    public static function error(?string $message = null, string $status = 'error', ServerStatusCodeEnum $statusCode = ServerStatusCodeEnum::BAD_REQUEST): ApiErrorResponse
    {
        return new ApiErrorResponse($message, $status, $statusCode);
    }

    public static function validationError(array $errors = [], string $status = 'error', ServerStatusCodeEnum $statusCode = ServerStatusCodeEnum::UNPROCESSABLE_CONTENT): ApiValidationErrorResponse
    {
        return new ApiValidationErrorResponse($errors, $status, $statusCode);
    }
}
