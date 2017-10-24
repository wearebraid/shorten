<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Base URL.
    |--------------------------------------------------------------------------
    |
    | This is the base domain url of the public directory. Typically it should
    | be a full domain name and nothing else. Do not include a trailing slash.
    |
    */
    'base_url' => 'http://example.com',

    /*
    |--------------------------------------------------------------------------
    | API Secret.
    |--------------------------------------------------------------------------
    |
    | The URL shortener authorization secret. This must be sent as a Bearer
    | token in all API requests in the Authorization header. There is only
    | one secret, and this is it. Pretty basic stuff folks.
    |
    */
    'api_secret' => 'put-a-long-string-here',

    /*
    |--------------------------------------------------------------------------
    | Database connection.
    |--------------------------------------------------------------------------
    |
    | These database credentials are used to access a database where hashes
    | are stored with their respective url redirects. By default, mysql is
    | recommended.
    |
    */
    'database' => [
        'driver'    => 'mysql',
        'host'      => 'mysql',
        'database'  => 'redirects',
        'username'  => 'your-username',
        'password'  => 'your-password',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]
];
