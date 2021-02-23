<?php

namespace Laraplate\Entities\Role\Providers;

use Illuminate\Support\ServiceProvider;
use Laraplate\Entities\Role\Contracts\RoleRepository;
use Laraplate\Entities\Role\Repositories\RoleRepositoryEloquent;

class RoleServiceProvider extends ServiceProvider
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
        $this->app->bind(RoleRepository::class, RoleRepositoryEloquent::class);
    }
}
