<?php

namespace Modules\Shared\Response;

use Modules\Shared\Enums\ServerStatusCodeEnum;

class ApiValidationErrorResponse implements ApiResponseInterface
{
    private readonly ?array $errors;

    private readonly string $status;

    private readonly ServerStatusCodeEnum $statusCode;

    public function __construct(?array $errors = null, string $status = 'error', ServerStatusCodeEnum $statusCode = ServerStatusCodeEnum::UNPROCESSABLE_CONTENT)
    {
        $this->errors = $errors;
        $this->status = $status;
        $this->statusCode = $statusCode;
    }

    public function getData()
    {
        return null;
    }

    public function getMessage()
    {
        return 'Validation failed';
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function toJson()
    {
        return response()->json([
            'status' => $this->getStatus(),
            'errors' => $this->getErrors(),
        ], $this->getStatusCode()->value);
    }
}
