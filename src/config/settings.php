<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Settings Driver
    |--------------------------------------------------------------------------
    |
    | Settings driver used to store persistent settings.
    |
    | Supported: "database"
    |
    */

    'default' => env('SETTINGS_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable caching
    |--------------------------------------------------------------------------
    |
    | If it is enabled all values gets cached after accessing it.
    |
    */
    'cache' => true,

    /*
    |--------------------------------------------------------------------------
    | Cache key prefix
    |--------------------------------------------------------------------------
    |
    | Cache key prefix used to store cached settings values.
    |
    */

    'prefix' => 'settings.',

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable value encryption
    |--------------------------------------------------------------------------
    |
    | If it is enabled all values gets encrypted and decrypted.
    |
    */
    'encryption' => true,

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable events
    |--------------------------------------------------------------------------
    |
    | If it is enabled various settings related events will be fired.
    |
    */
    'events' => true,

    /*
    |--------------------------------------------------------------------------
    | Repositories Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the driver information for each repository that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with this package. You are free to add more.
    |
    */

    'repositories' => [

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CONNECTION', 'mysql'),
            'table' => 'settings',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Override application config values
    |--------------------------------------------------------------------------
    |
    | If defined, settings package will override these config values from persistent
    | settings repository.
    |
    | Sample:
    |   "app.fallback_locale",
    |   "app.locale" => "settings.locale",
    |
    */

    'override' => [

    ],

];
