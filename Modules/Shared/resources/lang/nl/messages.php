<?php

return [
    'request' => [
        '200' => 'Success',
        '201' => 'Success',
        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '403' => 'Forbidden',
        '404' => 'Not found',
        '422' => 'Validation failed',
        '500' => 'Internal server error',
    ],
    'rule' => [
        'enum' => ':attribute is not valid',
    ],
    'error' => [
        'something_went_wrong' => 'Excuses, er is een onverwachte fout opgetreden. Probeer het opnieuw.',
    ],
];
