<?php

namespace Modules\Shared\Response;

class ApiValidationErrorResponse implements ApiResponseInterface
{
    private $errors;

    private $status;

    public function __construct($errors, $status = 422)
    {
        $this->errors = $errors;
        $this->status = $status;
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

    public function toJson()
    {
        return [
            'message' => $this->getMessage(),
            'status' => $this->getStatus(),
            'errors' => $this->errors,
        ];
    }
}
