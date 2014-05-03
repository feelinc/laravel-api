Laravel API
===========

Base library to start creating API using Laravel Framework. Plus support OAuth 2 authorization.

## Installation
### 1. Composer
Open your composer.json file and add the following lines:
```json
{
    "require": {
        "sule/api": "2.*",
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:feelinc/laravel-api.git",
            "options": {
                "ssl": {
                    "verify_peer": "false"
                }
            }
        }
    ]
    "minimum-stability": "stable"
}
```
Run composer update from the command line
```txt
composer update
```

### 2. Service Provider
Add the following to the list of service providers in "app/config/app.php".
```txt
'Sule\Api\ApiServiceProvider',
```

### 3. Configuration
After installing, you can publish the package's configuration file into your application, by running the following command:
```txt
php artisan config:publish sule/api
```