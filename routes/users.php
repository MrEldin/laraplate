<?php

use Laraplate\Api\V1\Controllers\UserController;

$api = app('Dingo\Api\Routing\Router');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are registered on Dingo's router. The file is loaded from the
| withRouting(then: ...) callback in bootstrap/app.php.
|
*/

$api->version('v1', function ($api) {
    $api->group([
        'middleware' => ['api'],
        'prefix'     => 'users',
        'as'         => 'users'
    ], function ($api) {
        $api->post('', UserController::class . '@create')->name('create');
        $api->put('{id}', UserController::class . '@update')->name('update');
        $api->get('', UserController::class . '@index')->name('index');
        $api->get('{id}', UserController::class . '@show')->name('show');
    });
});
