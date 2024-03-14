<?php

declare(strict_types=1);

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
    | Prefix of SSM parameter paths in environment variables
    |--------------------------------------------------------------------------
    |
    | For example, to load the value of the '/path/to/value' SSM parameter into
    | the 'ENV' environment variable, you must specify 'ssm:/path/to/value'.
    |
    | The 'ssm:' value can be changed here.
    */

    'prefix' => env('SSM_PARAMETERS_PREFIX', 'ssm:'),

    /*
    |--------------------------------------------------------------------------
    | AWS SDK Configuration
    |--------------------------------------------------------------------------
    */

    'ssm' => [
        'credentials' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'token' => env('AWS_SESSION_TOKEN'),
        ],
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        'version' => 'latest',
        'endpoint' => env('AWS_ENDPOINT'),
    ],

];
