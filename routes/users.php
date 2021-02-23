<?php

use Laraplate\Api\V1\Controllers\UserController;

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
        'prefix'     => 'users',
        'as'         => 'users'
    ], function ($api) {
        $api->post('', UserController::class . '@create')->name('create');
        $api->put('{id}', UserController::class . '@update')->name('update');
        $api->get('', UserController::class . '@index')->name('index');
        $api->get('{id}', UserController::class . '@show')->name('show');
    });
});
