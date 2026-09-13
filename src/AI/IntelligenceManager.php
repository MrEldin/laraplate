<?php

namespace Laraplate\AI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laraplate\AI\Agents\EntityAgent;
use Laraplate\AI\Context\EntityContext;
use Laraplate\AI\Context\EntityContextFactory;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Responses\AgentResponse;

use function Laravel\Ai\agent;

/**
 * The object behind the Intelligence facade.
 *
 * It is deliberately thin: it turns entities into EntityIntelligence instances
 * and forwards entity-free prompts to the Laravel AI SDK, so there is one entry
 * point for AI work regardless of whether an entity is involved.
 */
class IntelligenceManager
{
    public function __construct(protected readonly EntityContextFactory $contexts) {}

    /**
     * Start working with a single entity.
     */
    public function for(Model $entity): EntityIntelligence
    {
        return new EntityIntelligence($entity, $this->contexts);
    }

    /**
     * Get the redacted context describing an entity.
     */
    public function contextFor(Model $entity): EntityContext
    {
        return $this->contexts->make($entity);
    }

    /**
     * Ask a question that is not about any particular entity.
     *
     * @param  list<Tool>  $tools
     */
    public function ask(
        string $prompt,
        string $instructions = 'You are a helpful assistant.',
        array $tools = [],
    ): AgentResponse {
        return agent($instructions, tools: $tools)->prompt($prompt);
    }

    /**
     * Summarise the same question across several entities.
     *
     * @param  iterable<Model>  $entities
     * @return Collection<int, AgentResponse>
     */
    public function askEach(iterable $entities, string $question): Collection
    {
        return (new Collection($entities))
            ->map(fn (Model $entity): AgentResponse => $this->for($entity)->ask($question))
            ->values();
    }

    /**
     * Build an entity agent without going through the entity itself.
     *
     * @param  class-string<EntityAgent>  $agent
     */
    public function agent(string $agent, Model $entity, mixed ...$arguments): EntityAgent
    {
        return $this->for($entity)->agent($agent, ...$arguments);
    }
}
