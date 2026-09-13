<?php

namespace App\Providers;

use App\Console\Commands\ApiRoutesCommand;
use Dingo\Api\Transformer\Adapter\Fractal;
use Illuminate\Console\Application as Artisan;
use Illuminate\Foundation\Console\RouteListCommand;
use Illuminate\Support\ServiceProvider;
use Laraplate\Serializers\CustomSerializer;
use PHPOpenSourceSaver\Fractal\Manager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app['Dingo\Api\Transformer\Factory']->setAdapter(function () {
            $fractal = new Manager;
            $fractal->setSerializer(new CustomSerializer);

            return new Fractal($fractal);
        });

        $this->registerConsoleCommands();
    }

    /**
     * Dingo eagerly registers its route listing command, which inherits Laravel's
     * "route:list" signature and therefore takes over that command name. These
     * registrations run after Dingo's, restoring "route:list" and exposing the
     * Dingo listing under its intended "api:routes" name.
     */
    protected function registerConsoleCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        Artisan::starting(function (Artisan $artisan) {
            $artisan->addCommand($this->app->make(RouteListCommand::class));
            $artisan->addCommand($this->app->make(ApiRoutesCommand::class));
        });
    }
}
