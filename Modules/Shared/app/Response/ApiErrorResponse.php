<?php

namespace Modules\Shared\Response;

class ApiErrorResponse implements ApiResponseInterface
{
    private $message;

    private $status;

    private $statusCode;

    public function __construct(?string $message = null, string $status = 'error', int $statusCode = 400)
    {
        $this->message = $message;
        $this->status = $status;
        $this->statusCode = $statusCode;
    }

    public function getData()
    {
        return null;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function toJson()
    {
        return response()->json([
            'message' => $this->getMessage(),
            'status' => $this->getStatus(),
        ], $this->getStatusCode());
    }
}
