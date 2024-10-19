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
        'translatable_field_all' => 'Het veld :attribute moet voor alle talen worden ingevuld (:languages).',
        'translatable_field_none' => 'Het veld :attribute mag niet worden ingevuld.',
        'translatable_field_at_least' => 'Het veld :attribute moet voor minstens :minCount talen worden ingevuld.',
        'translatable_field_none_or_all' => 'Het veld :attribute moet voor alle talen of geen enkele taal worden ingevuld.',
        'translatable_field_at_least_locales' => 'Het veld :attribute moet voor minstens de volgende talen worden ingevuld: :locales.',
        'invalid_mode' => 'Ongeldig mode: :mode.',
    ],
    'error' => [
        'something_went_wrong' => 'Excuses, er is een onverwachte fout opgetreden. Probeer het opnieuw.',
        'model_not_found' => 'Model niet gevonden.',
        'invalid_resource_identifier' => 'Ongeldige resource-identificatie',
        'only_current_version_can_be_updated' => 'Alleen de huidige versie kan worden bijgewerkt.',
        'only_current_version_can_be_deleted' => 'Alleen de huidige versie kan worden verwijderd',
    ],
];
