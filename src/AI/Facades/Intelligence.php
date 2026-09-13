<?php

namespace Laraplate\AI\Facades;

use Illuminate\Support\Facades\Facade;
use Laraplate\AI\IntelligenceManager;

/**
 * The application's AI entry point.
 *
 * Named Intelligence rather than Ai so that it never collides with the SDK's
 * own Laravel\Ai\Ai facade when both are used in the same file.
 *
 * @method static \Laraplate\AI\EntityIntelligence for(\Illuminate\Database\Eloquent\Model $entity)
 * @method static \Laraplate\AI\Context\EntityContext contextFor(\Illuminate\Database\Eloquent\Model $entity)
 * @method static \Laravel\Ai\Responses\AgentResponse ask(string $prompt, string $instructions = 'You are a helpful assistant.')
 * @method static \Illuminate\Support\Collection askEach(iterable $entities, string $question)
 * @method static \Laraplate\AI\Agents\EntityAgent agent(string $agent, \Illuminate\Database\Eloquent\Model $entity, mixed ...$arguments)
 *
 * @see IntelligenceManager
 */
class Intelligence extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return IntelligenceManager::class;
    }
}
