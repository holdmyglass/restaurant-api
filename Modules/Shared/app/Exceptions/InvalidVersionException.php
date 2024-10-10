<?php

namespace Modules\Shared\Exceptions;

use Exception;

class InvalidVersionException extends Exception
{
    public function __construct($message = 'The model is old version and can not be updated')
    {
        parent::__construct($message, 422);
    }
}
