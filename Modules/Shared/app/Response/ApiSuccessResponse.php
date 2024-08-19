<?php

namespace Modules\Shared\Response;

class ApiSuccessResponse implements ApiResponseInterface
{
    private $data;

    private $message;

    private $status;

    public function __construct($data = null, $message = null, $status = 200)
    {
        $this->data = $data;
        $this->message = $message;
        $this->status = $status;
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

    public function toJson()
    {
        return [
            'data' => $this->data,
            'message' => $this->message,
            'status' => $this->status,
        ];
    }
}
