<?php

use Modules\Shared\Enums\TokenTypeEnum;

return [
    'name' => 'Shared',

    'token' => [

        'length' => [
            TokenTypeEnum::ACCESS_TOKEN->value => '200',
            TokenTypeEnum::RESET_TOKEN->value => '200',
            TokenTypeEnum::VERIFY_TOKEN->value => '200',
            TokenTypeEnum::ACCESS_CODE->value => '6',
            TokenTypeEnum::RESET_CODE->value => '6',
            TokenTypeEnum::VERIFY_CODE->value => '6',
        ],

        // Validity in seconds
        'validity' => [
            TokenTypeEnum::ACCESS_TOKEN->value => '2592000', // 30 days
            TokenTypeEnum::RESET_TOKEN->value => '3600', // 1 hour
            TokenTypeEnum::VERIFY_TOKEN->value => '86400', // 24 hours
            TokenTypeEnum::ACCESS_CODE->value => '3600', // 1 hour
            TokenTypeEnum::RESET_CODE->value => '3600', // 1 hour
            TokenTypeEnum::VERIFY_CODE->value => '3600', // 1 hour
        ],
    ],
];
