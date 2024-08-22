<?php

namespace Modules\Shared\Response;

class ApiSuccessResponse implements ApiResponseInterface
{
    private readonly ?array $data;

    private readonly string $message;

    private readonly string $status;

    private readonly int $statusCode;

    public function __construct(?array $data = null, ?string $message = null, string $status = 'success', int $statusCode = 200)
    {
        $this->data = $data;
        $this->message = $message;
        $this->status = $status;
        $this->statusCode = $statusCode;
    }

    public function getData()
    {
        return $this->data;
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
            'data' => $this->getData(),
            'message' => $this->getMessage(),
            'status' => $this->getStatus(),
        ], $this->getStatusCode());
    }
}
