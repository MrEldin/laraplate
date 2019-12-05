<?php

namespace SmartlyJobs\Entities\Permission\Providers;

use Illuminate\Support\ServiceProvider;
use SmartlyJobs\Entities\Permission\Contracts\PermissionRepository;
use SmartlyJobs\Entities\Permission\Repositories\PermissionRepositoryEloquent;

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
