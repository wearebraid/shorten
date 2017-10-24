# Shorten
---------
[![Build Status](https://scrutinizer-ci.com/g/wearebraid/shorten/badges/build.png?b=master)](https://scrutinizer-ci.com/g/wearebraid/shorten/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/wearebraid/shorten/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/wearebraid/shorten/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/wearebraid/shorten/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/wearebraid/shorten/?branch=master)
[![License](https://poser.pugx.org/braid/shorten/license)](https://packagist.org/packages/braid/shorten)


Need an easy to use custom URL shortener like bit.ly, t.co, goo.gl? Shorten is a simple, PSR-7 and PSR-15 compliant headless URL shortener made for developers like you.

## Installation

### Download

Shorten is a small micro application. It is easily installed with composer’s `create-project` command.

```sh
composer create-project braid/shorten your-shortener-name
```

### Configuration

Shorten comes with two example configuration files. To configure your instance, just copy these files:

```sh
cp config.example.php config.php
cp phinx.example.yml phinx.yml
```

#### config.php

Edit your newly created `config.php` as appropriate.

Option        | Description
--------------|--------------------------------------
`base_url`    | You'll want to set your application’s `base_url` to the domain name you are using as a shortener (http://bit.ly for example).
`api_secret`  | Choose a randomly generated key (>32 bytes recommended). This will be your application’s `Bearer` token for api requests.
`database`    | Shorten uses Laravel’s excellent [Eloquent ORM](https://laravel.com/docs/5.5/eloquent). Configure the database credentials here. MySQL/MariaDB is recommended for medium/high traffic, but a simple SQLite is also supported.

Pro Tip: here’s a quick way to generate an api key:

```sh
php -r 'echo bin2hex(openssl_random_pseudo_bytes(32)) . "\n";'
```

#### phinx.yml

Shorten uses Rob Morgan’s [Phinx](https://phinx.org/) for database migrations. Phinx has its own configuration, but the values should be the same as your `config.php` `database` settings.

### Database

Once your configuration files have been created and updated, go ahead and create the database you specified in the above configuration file. Then simply run the migrations:

```sh
vendor/bin/phinx migrate
```

### Server

The last step is to simply point an http server or virtual host to the `public` directory. This documentation won't go into detail on how to setup Apache or nginx.

## Api

Shorten comes with a simple API for creating, listing, and removing shortened urls.

Method   | Endpoint      | Description
---------|---------------|-------------------------------------
`GET`    | `/resources`  | List all shortened URLs currently stored.
`POST`   | `/resources`  | Creates a new URL. Must include a JSON body with a `url` attribute.
`DELETE` | `/resources`  | Removes a shortened URL. Must include a JSON body with an `id` attribute.

All api requests MUST include an `Authorization` header in the format:

```
Authorization: Bearer api_secret_here
```

The return format for redirect resources is JSON. Example:

```json
{
  "id": 1,
  "hash": "ce24227c",
  "redirect_to": "http://www.google.com",
  "count": 0,
  "url": "http://bit.ly/ce24227c"
}
```

_Note: Yes `DELETE /resources/{id}` would be cleaner, but using simple strings allows for faster routing._

## Best practices

Shorten is intended to be a simple set-and-forget service you can use internally at your organization or embed into the apps you build. Keep in mind it is a project and not a dependency, so you are free and encouraged to bolt on additional [PSR-15 middleware](https://github.com/middlewares/psr15-middlewares) if you want.

## Sponsor

Shorten is written and maintained by [Braid LLC](https://www.wearebraid.com), and offered freely under an MIT license.

[<img src="https://assets.wearebraid.com/sig.png" title="Written and maintained by Braid LLC" alt="Braid LLC" width="150" height="44">](https://www.wearebraid.com)