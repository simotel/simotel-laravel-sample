<?php

return [
    'smartApi' => [
        'apps' => [
            '*' => "\YourApp\SmartApiAppClass",
        ],
    ],
    'ivrApi' => [
        'apps' => [
            '*' => "\YourApp\IvrApiAppClass",
        ],
    ],
    'trunkApi' => [
        'apps' => [
            '*' => "\YourApp\TrunkApiApp",
        ],
    ],
    'extensionApi' => [
        'apps' => [
            '*' => "\YourApp\ExtensionApiAppClass",
        ],
    ],
    'simotelApi' => [
        'server_address' => 'http://yourSimotelServer/api/v4',
        'api_auth' => 'basic',  // simotel api authentication: basic,token,both
        'api_user' => 'apiUser',
        'api_pass' => 'apiPass',
        'api_key' => 'apiToken',
    ],
];