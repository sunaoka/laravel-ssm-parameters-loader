<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable or not
    |--------------------------------------------------------------------------
    */
    'enable' => env('SSM_PARAMETERS_ENABLE', true),

    /*
    |--------------------------------------------------------------------------
    | Cache expiration seconds
    |--------------------------------------------------------------------------
    */
    'ttl' => env('SSM_PARAMETERS_CACHE_TTL', 0),

    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    */

    'ssm' => [
        'credentials' => [
            'key'    => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'token'  => env('AWS_SESSION_TOKEN'),
        ],
        'region'      => env('AWS_DEFAULT_REGION', 'us-east-1'),
        'version'     => 'latest',
        'endpoint'    => env('AWS_ENDPOINT'),
    ],

];
