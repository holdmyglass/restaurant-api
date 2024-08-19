<?php

namespace Modules\Shared\Response;

class ApiErrorResponse implements ApiResponseInterface
{
    private $message;

    private $status;

    public function __construct($message = null, $status = 400)
    {
        $this->message = $message;
        $this->status = $status;
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

    public function toJson()
    {
        return [
            'message' => $this->message,
            'status' => $this->status,
        ];
    }
}
