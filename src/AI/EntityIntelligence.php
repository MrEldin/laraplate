<?php

namespace Laraplate\AI;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Laraplate\AI\Agents\EntityAgent;
use Laraplate\AI\Agents\EntityClassificationAgent;
use Laraplate\AI\Agents\EntityQuestionAgent;
use Laraplate\AI\Agents\EntitySummaryAgent;
use Laraplate\AI\Context\EntityContext;
use Laraplate\AI\Context\EntityContextFactory;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Responses\AgentResponse;
use Laravel\Ai\Responses\QueuedAgentResponse;
use Laravel\Ai\Responses\StreamableAgentResponse;
use Laravel\Ai\Responses\StructuredAgentResponse;
use RuntimeException;

/**
 * The fluent AI surface for a single entity.
 *
 * Reached either from the entity itself, via the HasAi trait:
 *
 *     $user->ai()->ask('Has this account been used recently?');
 *
 * or from anywhere else -- a service, a job, a command -- via the facade:
 *
 *     Intelligence::for($user)->summarize();
 *
 * Both paths build the same redacted context, so an entity is described to the
 * model the same way no matter who asked.
 */
class EntityIntelligence
{
    /** @var list<Tool> */
    protected array $tools = [];

    protected Lab|array|string|null $provider = null;

    protected ?string $model = null;

    public function __construct(
        protected readonly Model $entity,
        protected readonly EntityContextFactory $contexts,
    ) {}

    /**
     * Attach extra tools to whichever agent runs next.
     */
    public function withTools(Tool ...$tools): static
    {
        $this->tools = [...$this->tools, ...$tools];

        return $this;
    }

    /**
     * Pin the provider (and optionally the model) for the next run.
     */
    public function using(Lab|array|string $provider, ?string $model = null): static
    {
        $this->provider = $provider;
        $this->model = $model;

        return $this;
    }

    /**
     * Get the redacted context that agents will receive.
     */
    public function context(): EntityContext
    {
        return $this->contexts->make($this->entity)->withTools($this->tools);
    }

    /**
     * Build one of this package's entity agents, bound to this entity.
     *
     * @param  class-string<EntityAgent>  $agent
     */
    public function agent(string $agent, mixed ...$arguments): EntityAgent
    {
        if (! is_subclass_of($agent, EntityAgent::class)) {
            throw new RuntimeException("[{$agent}] must extend ".EntityAgent::class.'.');
        }

        return new $agent($this->context(), ...$arguments);
    }

    /**
     * Ask a free-form question about this entity.
     */
    public function ask(string $question, array $attachments = []): AgentResponse
    {
        return $this->prompt($this->agent(EntityQuestionAgent::class), $question, $attachments);
    }

    /**
     * Ask a free-form question and stream the answer.
     */
    public function stream(string $question, array $attachments = []): StreamableAgentResponse
    {
        return $this->agent(EntityQuestionAgent::class)->stream(
            $question, $attachments, $this->provider, $this->model
        );
    }

    /**
     * Ask a free-form question on the queue.
     */
    public function queue(string $question, array $attachments = []): QueuedAgentResponse
    {
        return $this->agent(EntityQuestionAgent::class)->queue(
            $question, $attachments, $this->provider, $this->model
        );
    }

    /**
     * Ask a free-form question and broadcast the streamed answer.
     */
    public function broadcast(string $question, Channel|array $channels, array $attachments = []): StreamableAgentResponse
    {
        return $this->agent(EntityQuestionAgent::class)->broadcast(
            $question, $channels, $attachments, provider: $this->provider, model: $this->model
        );
    }

    /**
     * Summarise this entity into a structured response.
     */
    public function summarize(): StructuredAgentResponse
    {
        return $this->prompt(
            $this->agent(EntitySummaryAgent::class),
            'Summarise this record.',
        );
    }

    /**
     * Classify this entity into exactly one of the given labels.
     *
     * @param  list<string>  $labels
     */
    public function classify(array $labels, ?string $question = null): StructuredAgentResponse
    {
        if ($labels === []) {
            throw new RuntimeException('At least one label is required to classify an entity.');
        }

        return $this->prompt(
            $this->agent(EntityClassificationAgent::class, $labels),
            $question ?? 'Classify this record.',
        );
    }

    /**
     * Run a prompt through the given agent with this instance's provider settings.
     *
     * @template TResponse of AgentResponse
     *
     * @return TResponse
     */
    protected function prompt(EntityAgent $agent, string $prompt, array $attachments = []): AgentResponse
    {
        return $agent->prompt($prompt, $attachments, $this->provider, $this->model);
    }
}
