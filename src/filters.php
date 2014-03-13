<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::after(function($request, $response)
{
    if ($request->isMethod('OPTIONS')) {
        $headers = App::make('api')->getConfig('headers');

        if ( ! empty($headers)) {
            foreach ($headers as $key => $value) {
                $response->header($key, $value);
            }
        }

        unset($headers);
    }
});

/*
|--------------------------------------------------------------------------
| OAuth Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify OAuth token.
|
*/

Route::filter('api.oauth', function() {
    $argList = array();
    
    if (func_num_args() > 0) {
        $argList = func_get_args();

        unset($argList[0]);
        unset($argList[1]);
    }
    
    return App::make('api')->validateAccessToken($argList);
});

/*
|--------------------------------------------------------------------------
| Request Header and Content Validator Filters
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
    if ( ! App::make('api')->isValidMD5()) {
        // 400 Bad Request - The request is malformed, such as if the body does not parse
        return Response::make('', 400);
    }
});

Route::filter('api.ua.required', function() {
    if ( ! App::make('api')->getRequest()->validateUserAgent()) {
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
