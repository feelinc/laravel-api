Laravel API
===========

Base library to start creating API using Laravel Framework. Plus support OAuth 2 authorization.

## Installation

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