<?php

namespace Laraplate\AI;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Laraplate\AI\Agents\EntityAgent;
use Laraplate\AI\AgentOptions;
use Laraplate\AI\Agents\EntityClassificationAgent;
use Laraplate\AI\Agents\EntityQuestionAgent;
use Laraplate\AI\Agents\EntitySummaryAgent;
use Laraplate\AI\Context\EntityContext;
use Laraplate\AI\Context\EntityContextFactory;
use Laravel\Ai\Approvals\Decisions;
use Laravel\Ai\Ai as AiFacade;
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

    protected AgentOptions $options;

    protected bool $remembers = false;

    protected ?object $participant = null;

    protected ?string $conversationId = null;

    protected bool $continueLast = false;

    public function __construct(
        protected readonly Model $entity,
        protected readonly EntityContextFactory $contexts,
    ) {
        $this->options = new AgentOptions;
    }

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
     * Use the provider's cheapest model for the next run.
     *
     * Good for high-volume classification where the smartest model is wasted.
     */
    public function cheap(): static
    {
        $this->options = $this->options->with(modelTier: AgentOptions::TIER_CHEAPEST);

        return $this;
    }

    /**
     * Use the provider's smartest model for the next run.
     */
    public function smart(): static
    {
        $this->options = $this->options->with(modelTier: AgentOptions::TIER_SMARTEST);

        return $this;
    }

    /**
     * Cap how many tool-calling rounds the agent may take.
     */
    public function maxSteps(int $steps): static
    {
        $this->options = $this->options->with(maxSteps: $steps);

        return $this;
    }

    /**
     * Cap the number of output tokens.
     */
    public function maxTokens(int $tokens): static
    {
        $this->options = $this->options->with(maxTokens: $tokens);

        return $this;
    }

    /**
     * Set the sampling temperature.
     */
    public function temperature(float $temperature): static
    {
        $this->options = $this->options->with(temperature: $temperature);

        return $this;
    }

    /**
     * Remember this exchange, so follow-up questions keep their context.
     *
     * Without a participant the SDK does not persist anything, so memory is
     * opt-in per call chain rather than a property of the entity.
     *
     *     $thread = $user->ai()->remember();
     *     $thread->ask('Why was their payment declined?');
     *     $thread->ask('And what happened before that?');   // knows the context
     */
    public function remember(?object $as = null): static
    {
        $this->remembers = true;
        $this->participant = $as ?? $this->entity;

        return $this;
    }

    /**
     * Resume a stored conversation by its UUID.
     */
    public function continueConversation(string $conversationId, ?object $as = null): static
    {
        $this->remembers = true;
        $this->conversationId = $conversationId;
        $this->participant = $as ?? $this->entity;

        return $this;
    }

    /**
     * Resume the participant's most recent conversation.
     */
    public function continueLastConversation(?object $as = null): static
    {
        $this->remembers = true;
        $this->participant = $as ?? $this->entity;
        $this->conversationId = null;
        $this->continueLast = true;

        return $this;
    }

    /**
     * Get the UUID of the conversation this instance is on, once one exists.
     */
    public function conversationId(): ?string
    {
        return $this->conversationId;
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

        return (new $agent($this->context(), ...$arguments))->withOptions($this->options);
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
        return $this->configure($this->agent(EntityQuestionAgent::class))->stream(
            $question, $attachments, $this->provider, $this->resolveModel()
        );
    }

    /**
     * Ask a free-form question on the queue.
     */
    public function queue(string $question, array $attachments = []): QueuedAgentResponse
    {
        return $this->configure($this->agent(EntityQuestionAgent::class))->queue(
            $question, $attachments, $this->provider, $this->resolveModel()
        );
    }

    /**
     * Ask a free-form question and broadcast the streamed answer.
     */
    public function broadcast(string $question, Channel|array $channels, array $attachments = []): StreamableAgentResponse
    {
        return $this->configure($this->agent(EntityQuestionAgent::class))->broadcast(
            $question, $channels, $attachments, provider: $this->provider, model: $this->resolveModel()
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
    protected function prompt(EntityAgent $agent, Decisions|string $prompt, array $attachments = []): AgentResponse
    {
        $response = $this->configure($agent)->prompt(
            $prompt, $attachments, $this->provider, $this->resolveModel()
        );

        // Capture the conversation the SDK created so the next call on this
        // instance continues it instead of starting over.
        if ($this->remembers && $response->conversationId !== null) {
            $this->conversationId = $response->conversationId;
            $this->continueLast = false;
        }

        return $response;
    }

    /**
     * Apply this instance's conversation settings to the agent.
     */
    protected function configure(EntityAgent $agent): EntityAgent
    {
        if (! $this->remembers) {
            return $agent;
        }

        return match (true) {
            $this->conversationId !== null => $agent->continue($this->conversationId, $this->participant),
            $this->continueLast => $agent->continueLastConversation($this->participant),
            default => $agent->forParticipant($this->participant),
        };
    }

    /**
     * Resolve the model for this run, honouring any requested tier.
     */
    protected function resolveModel(): ?string
    {
        if ($this->model !== null) {
            return $this->model;
        }

        if ($this->options->modelTier === null) {
            return null;
        }

        $provider = AiFacade::textProvider(is_string($this->provider) ? $this->provider : null);

        return $this->options->resolveModel($provider);
    }

    /**
     * Resume a run that paused waiting for tool approvals.
     *
     *     $response = $user->ai()->ask('Suspend them if this looks like fraud.');
     *
     *     if ($response->hasPendingApprovals()) {
     *         $user->ai()->resume(Decisions::from([$id => true]));
     *     }
     *
     * @param  array<string, bool>|Decisions  $decisions
     */
    public function resume(array|Decisions $decisions, string $agent = EntityQuestionAgent::class): AgentResponse
    {
        return $this->prompt(
            $this->agent($agent),
            is_array($decisions) ? Decisions::from($decisions) : $decisions,
        );
    }
}
