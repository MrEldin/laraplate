<?php

namespace Laraplate\Entities\User\Providers;

use Illuminate\Support\ServiceProvider;
use Laraplate\Entities\User\Contracts\UserRepository;
use Laraplate\Entities\User\Repositories\UserRepositoryEloquent;

class UserServiceProvider extends ServiceProvider
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
        $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);
    }
}
