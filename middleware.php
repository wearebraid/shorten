<?php

use App\Middleware\Api;
use App\Middleware\Database;
use App\Middleware\Redirect;
use App\Middleware\BodyParser;

return [
    new Database(),
    new BodyParser(),
    new Api(),
    new Redirect()
];
