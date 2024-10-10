<?php

namespace Modules\Shared\Response;

use Modules\Shared\Enums\ServerStatusCodeEnum;

class ApiSuccessResponse implements ApiResponseInterface
{
    private readonly ?array $data;

    private readonly ?string $message;

    private readonly string $status;

    private readonly ServerStatusCodeEnum $statusCode;

    public function __construct(?array $data = null, ?string $message = null, string $status = 'success', ServerStatusCodeEnum $statusCode = ServerStatusCodeEnum::OK)
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
        ], $this->getStatusCode()->value);
    }
}
