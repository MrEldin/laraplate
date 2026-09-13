<?php


use Laraplate\Api\V1\Controllers\AuthController;

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
    $api->post('login', AuthController::class . '@login');

    $api->group(['middleware' => ['api'], 'prefix' => 'auth'], function ($api) {
        $api->post('logout', AuthController::class . '@logout');
        $api->get('refresh', AuthController::class . '@refresh');
        $api->get('user', AuthController::class . '@user');
    });
});
