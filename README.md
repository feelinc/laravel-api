Laravel API
===========

Base library to start creating API using Laravel Framework. Plus support OAuth 2 authorization.

## Installation

### 1. Composer
Open your composer.json file and add the following lines:
```json
{
    "require": {
        "sule/api": "2.0.0",
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
}e
```
Run composer update from the command line
```
composer update
```

### 2. Service Provider & Aliases
Add the following to the list of service providers in "app/config/app.php".
```
'Sule\Api\ApiServiceProvider',
```
Add the following to the list of aliases in "app/config/app.php".
```
'API'             => 'Sule\Api\Facades\API'
```

### 3. Create Tables
Create all required tables from "TABLES.sql" file.

### 4. Configuration
After installing, you can publish the package's configuration file into your application, by running the following command:
```
php artisan config:publish sule/api
```

## Routes
POST /authorization

### Request Headers
```
User-Agent: My User Agent 
Content-MD5: md5($stringContent.$clientSecret) 
```

### Request Body
```
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

### 1. api.oauth
Check route againts authorized client and passed scope, in example checking current client is having "read" scope:
```php
Route::get('api/v1/users', array(
    'before' => array(
        'api.oauth:read'
    ), function() {

    }
));
```

### 2. api.content.md5
Check request content signature at "Content-MD5" header. Signature should be md5($stringContent.$clientSecret)
```php
Route::post('api/v1/users', array(
    'before' => array(
        'api.content.md5', 
        'api.oauth:write'
    ), function() {

    }
));
```

### 3. api.ua.required
Check request "User-Agent" header.
```php
Route::post('api/v1/users', array(
    'before' => array(
        'api.ua.required', 
        'api.content.md5', 
        'api.oauth:write'
    ), function() {

    }
));
```

### 4. api.limit
Check request not exceed the limit per each client.
```php
Route::post('api/v1/users', array(
    'before' => array(
        'api.ua.required', 
        'api.content.md5', 
        'api.limit', 
        'api.oauth:write'
    ), function() {

    }
));
```

## Response
Return JSON response including Limiter and Access-Control-Expose-Headers header

### 1. Single JSON object
```php
return API::resourceJson($data = array(), $status = 200, array $headers = array());
```

### 2. Collection JSON object
```php
return API::collectionJson($data = array(), $status = 200, array $headers = array());
```
