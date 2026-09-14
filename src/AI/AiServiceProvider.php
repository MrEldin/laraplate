<?php

namespace Laraplate\AI;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laraplate\AI\Console\IndexEntitiesCommand;
use Laraplate\AI\Context\EntityContextFactory;
use Laraplate\AI\Listeners\RecordAiInvocation;
use Laraplate\AI\Search\EntityVectorIndex;
use Laravel\Ai\Events\AgentFailed;
use Laravel\Ai\Events\AgentPrompted;
use Laravel\Ai\Events\PromptingAgent;

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
        $this->mergeConfigFrom(__DIR__.'/../../config/intelligence.php', 'intelligence');

        $this->app->singleton(EntityContextFactory::class);
        $this->app->singleton(EntityVectorIndex::class);

        $this->app->singleton(
            IntelligenceManager::class,
            fn ($app): IntelligenceManager => new IntelligenceManager($app->make(EntityContextFactory::class)),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([IndexEntitiesCommand::class]);
        }

        $this->recordInvocations();
    }

    /**
     * Log every agent run and its token usage, unless disabled.
     */
    protected function recordInvocations(): void
    {
        if (! config('intelligence.logging.enabled', true)) {
            return;
        }

        Event::listen(PromptingAgent::class, [RecordAiInvocation::class, 'handleStart']);
        Event::listen(AgentPrompted::class, [RecordAiInvocation::class, 'handleSuccess']);
        Event::listen(AgentFailed::class, [RecordAiInvocation::class, 'handleFailure']);
    }
}
