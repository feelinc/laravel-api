<?php

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
        return Response::make('', 429);
    }
});
