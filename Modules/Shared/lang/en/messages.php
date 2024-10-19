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
        'translatable_field_all' => ':attribute must be present for all languages: [:languages]',
        'translatable_field_none' => ':attribute must not be present for any language.',
        'translatable_field_at_least' => ':attribute must be present for at least :minCount languages.',
        'translatable_field_none_or_all' => ':attribute must be either present for all languages or not present for any language.',
        'translatable_field_at_least_locales' => ':attribute must be present for at least the following locales: :locales',
        'invalid_mode' => 'Invalid mode: :mode',
    ],
    'error' => [
        'something_went_wrong' => 'Apologies, an unexpected error occurred. Please try again.',
        'model_not_found' => 'Model not found',
        'invalid_resource_identifier' => 'Invalid resource identifier',
        'only_current_version_can_be_updated' => 'Only the current version can be updated.',
        'only_current_version_can_be_deleted' => 'Only the current version can be deleted.',
    ],
];
