<?php

use Laraplate\Api\V1\Controllers\RoleController;

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
        'prefix'     => 'roles',
        'as'         => 'roles'
    ], function ($api) {
        $api->post('', RoleController::class . '@create')->name('create-role');
        $api->put('{id}', RoleController::class . '@update')->name('update-role');
        $api->get('', RoleController::class . '@index')->name('index-role');
        $api->get('{id}', RoleController::class . '@show')->name('show-role');
        $api->delete('{id}', RoleController::class . '@destroy')->name('delete-role');
        $api->post('{roleId}/permissions/{permissionId}', RoleController::class . '@syncPermission')->name('sync-permission');
        $api->put('{roleId}/permissions/{permissionId}', RoleController::class . '@attachPermission')->name('attach-permission');
        $api->delete('{roleId}/permissions/{permissionId}', RoleController::class . '@detachPermission')->name('detach-permission');
    });
});
