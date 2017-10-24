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
    'base_url' => 'http://brd.bz',
    
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
    'api_secret' => '9575d687c61ce66fc190cd2bed464cef',

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
        'driver'    => 'sqlite',
        'database'  => __DIR__ . '/../storage/redirects.sqlite',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ]];
