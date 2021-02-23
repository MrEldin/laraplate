<?php


use Laraplate\Api\V1\Controllers\AuthController;

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
    $api->post('login', AuthController::class . '@login');

    $api->group(['middleware' => ['api'], 'prefix' => 'auth'], function ($api) {
        $api->post('logout', AuthController::class . '@logout');
        $api->get('refresh', AuthController::class . '@refresh');
        $api->get('user', AuthController::class . '@user');
    });
});
