<?php

namespace SmartlyJobs\Entities\Role\Providers;

use Illuminate\Support\ServiceProvider;
use SmartlyJobs\Entities\Role\Contracts\RoleRepository;
use SmartlyJobs\Entities\Role\Repositories\RoleRepositoryEloquent;

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
