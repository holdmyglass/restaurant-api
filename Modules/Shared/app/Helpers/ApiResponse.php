<?php

namespace Modules\Shared\Helpers;

use Modules\Shared\Response\ApiErrorResponse;
use Modules\Shared\Response\ApiSuccessResponse;
use Modules\Shared\Response\ApiValidationErrorResponse;

class ApiResponse
{
    public static function success($data = null, $message = null, $status = 200): ApiSuccessResponse
    {
        return new ApiSuccessResponse($data, $message, $status);
    }

    public static function error($message = null, $status = 400): ApiErrorResponse
    {
        return new ApiErrorResponse($message, $status);
    }

    public static function validationError($errors, $status = 422): ApiValidationErrorResponse
    {
        return new ApiValidationErrorResponse($errors, $status);
    }
}
