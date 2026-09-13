<?php

use Laraplate\Api\V1\Controllers\PermissionController;

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
        'prefix'     => 'permissions',
        'as'         => 'permissions'
    ], function ($api) {
        $api->post('', PermissionController::class . '@create')->name('create-permission');
        $api->put('{id}', PermissionController::class . '@update')->name('update-permission');
        $api->get('', PermissionController::class . '@index')->name('index-permission');
        $api->get('{id}', PermissionController::class . '@show')->name('show-permission');
    });
});
