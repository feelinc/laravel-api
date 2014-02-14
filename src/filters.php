<?php

/*
|--------------------------------------------------------------------------
| Request Header and Content Validator
|--------------------------------------------------------------------------
|
| The following filters are used to verify we receive a good header and 
| content.
|
*/

Route::filter('api.content.form', function() {
    if ( ! App::make('api')->getRequest()->isFormRequest()) {
        // 415 Unsupported Media Type - If incorrect content type was provided as part of the request
        return Response::make('', 415);
    }
});

Route::filter('api.content.json', function() {
    if ( ! App::make('api')->getRequest()->isJson()) {
        // 415 Unsupported Media Type - If incorrect content type was provided as part of the request
        return Response::make('', 415);
    }
});

Route::filter('api.content.md5', function() {
    if ( ! App::make('api')->getRequest()->validateMD5Data()) {
        // 400 Bad Request - The request is malformed, such as if the body does not parse
        return Response::make('', 400);
    }
});

/*
|--------------------------------------------------------------------------
| Request Limit Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the client request is not
| reach their limit.
|
*/

Route::filter('api.limit', function() {
    if (App::make('api')->checkRequestLimit()) {
        // 429 Too Many Requests - When a request is rejected due to rate limiting
        return Response::make('', 429);
    }
});
