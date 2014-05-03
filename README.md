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

## Routes
POST /authorization

### Request Headers
```txt
User-Agent: My User Agent 
Content-MD5: md5($stringContent.$clientSecret) 
```

### Request Body
```txt
grant_type:    client_credentials 
client_id:     JXSb6nEzpQ0e3WAWjsSsZurCaLy0knDjzkwxRlJs 
client_secret: C4vpZLRI2kncfXJQZ9l0hdnaTCTupyqF1deCVEPf 
```

### Response Body
```json
{
    "access_token": "jU5vKEBSPSVqRwEXwjIM0N1YefCG0hwqTK5i0UC3",
    "token_type": "bearer",
    "expires": 1399017374,
    "expires_in": 3600
}
```

## Filters

### 2. api.oauth
Check route againts authorized client and passed scope, in example:
```php
Route::get('api/v1/users', array(
    'before' => array(
        'api.oauth:read'
    ), function() {

        

    }
));
```