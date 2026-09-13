<?php

return [
    App\Providers\AppServiceProvider::class,
    Laraplate\AI\AiServiceProvider::class,
    Laraplate\Entities\User\Providers\UserServiceProvider::class,
    Laraplate\Entities\Role\Providers\RoleServiceProvider::class,
    Laraplate\Entities\Permission\Providers\PermissionServiceProvider::class,
];
