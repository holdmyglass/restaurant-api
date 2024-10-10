<?php

namespace Modules\Shared\Response;

use Modules\Shared\Enums\ServerStatusCodeEnum;

class ApiErrorResponse implements ApiResponseInterface
{
    private readonly ?string $message;

    private readonly string $status;

    private readonly ServerStatusCodeEnum $statusCode;

    public function __construct(?string $message = null, string $status = 'error', ServerStatusCodeEnum $statusCode = ServerStatusCodeEnum::BAD_REQUEST)
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
        ], $this->getStatusCode()->value);
    }
}
