<?php

return [
    'tenant_key' => 'organization_id',
    'hotel_key' => 'hotel_id',

    'headers' => [
        'organization' => 'X-Organization-ID',
        'hotel' => 'X-Hotel-ID',
    ],

    'cache_ttl' => [
        'tenant_context' => 300,
        'hotel_access' => 300,
    ],
];
