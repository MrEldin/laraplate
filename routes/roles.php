<?php

use Laraplate\Api\V1\Controllers\RoleController;

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
