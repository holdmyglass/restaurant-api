<?php

namespace Modules\Shared\Response;

class ApiValidationErrorResponse implements ApiResponseInterface
{
    private readonly ?array $errors;

    private readonly string $status;

    private readonly int $statusCode;

    public function __construct(?array $errors = null, string $status = 'success', int $statusCode = 200)
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
        ], $this->getStatusCode());
    }
}
