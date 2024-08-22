<?php

namespace Modules\Shared\Helpers;

use Modules\Shared\Response\ApiErrorResponse;
use Modules\Shared\Response\ApiSuccessResponse;
use Modules\Shared\Response\ApiValidationErrorResponse;

class ApiResponse
{
    public static function success(?array $data = null, ?string $message = null, string $status = 'success', int $statusCode = 200): ApiSuccessResponse
    {
        return new ApiSuccessResponse($data, $message, $status, $statusCode);
    }

    public static function error(?string $message = null, string $status = 'error', int $statusCode = 400): ApiErrorResponse
    {
        return new ApiErrorResponse($message, $status, $statusCode);
    }

    public static function validationError(array $errors = [], string $status = 'error', int $statusCode = 422): ApiValidationErrorResponse
    {
        return new ApiValidationErrorResponse($errors, $status, $statusCode);
    }
}
