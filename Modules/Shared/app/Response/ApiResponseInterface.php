<?php

namespace Modules\Shared\Response;

interface ApiResponseInterface
{
    public function getData();

    public function getMessage();

    public function getStatus();

    public function toJson();
}
