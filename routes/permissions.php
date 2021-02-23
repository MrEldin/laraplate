<?php

use Laraplate\Api\V1\Controllers\PermissionController;

$api = app('Dingo\Api\Routing\Router');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
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
