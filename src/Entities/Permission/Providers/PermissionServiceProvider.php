<?php

namespace Laraplate\Entities\Permission\Providers;

use Illuminate\Support\ServiceProvider;
use Laraplate\Entities\Permission\Contracts\PermissionRepository;
use Laraplate\Entities\Permission\Repositories\PermissionRepositoryEloquent;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PermissionRepository::class, PermissionRepositoryEloquent::class);
    }
}
