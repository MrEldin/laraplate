<?php

namespace Laraplate\AI;

use Illuminate\Support\ServiceProvider;
use Laraplate\AI\Context\EntityContextFactory;

/**
 * Wires the application's AI layer on top of the Laravel AI SDK.
 */
class AiServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(EntityContextFactory::class);

        $this->app->singleton(
            IntelligenceManager::class,
            fn ($app): IntelligenceManager => new IntelligenceManager($app->make(EntityContextFactory::class)),
        );
    }
}
