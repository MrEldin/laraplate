<?php

namespace Laraplate\AI\Listeners;

use Laraplate\AI\Agents\EntityAgent;
use Laraplate\AI\Models\AiInvocation;
use Laravel\Ai\Events\AgentFailed;
use Laravel\Ai\Events\AgentPrompted;
use Laravel\Ai\Events\PromptingAgent;
use Laravel\Ai\Prompts\AgentPrompt;

/**
 * Writes one row per agent run, so token spend can be attributed to the agent
 * and the record that caused it rather than showing up as one opaque bill.
 *
 * Start times are held in memory keyed by invocation id; a run that never
 * completes simply leaves no row, which is the correct outcome for a process
 * that died mid-request.
 */
class RecordAiInvocation
{
    /** @var array<string, float> */
    protected static array $startedAt = [];

    /**
     * Note when a run began.
     */
    public function handleStart(PromptingAgent $event): void
    {
        static::$startedAt[$event->invocationId] = microtime(true);
    }

    /**
     * Record a completed run and its token usage.
     */
    public function handleSuccess(AgentPrompted $event): void
    {
        $usage = $event->response->usage;

        $this->record($event->invocationId, $event->prompt, [
            'prompt_tokens' => $usage->promptTokens,
            'completion_tokens' => $usage->completionTokens,
            'cache_read_tokens' => $usage->cacheReadInputTokens,
            'cache_write_tokens' => $usage->cacheWriteInputTokens,
            'reasoning_tokens' => $usage->reasoningTokens,
        ]);
    }

    /**
     * Record a failed run, which still costs time and often tokens.
     */
    public function handleFailure(AgentFailed $event): void
    {
        $this->record($event->invocationId, $event->prompt, [
            'failed' => true,
            'error' => $event->exception->getMessage(),
        ]);
    }

    /**
     * Persist the row.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function record(string $invocationId, AgentPrompt $prompt, array $attributes): void
    {
        $agent = $prompt->agent;
        $subject = $agent instanceof EntityAgent ? $agent->context->entity : null;

        AiInvocation::query()->create([
            'invocation_id' => $invocationId,
            'agent' => $agent::class,
            'provider' => $prompt->provider->name(),
            'model' => $prompt->model,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'duration_ms' => $this->elapsed($invocationId),
            ...$attributes,
        ]);
    }

    /**
     * Milliseconds since the run started, when the start was seen.
     */
    protected function elapsed(string $invocationId): ?int
    {
        $startedAt = static::$startedAt[$invocationId] ?? null;

        unset(static::$startedAt[$invocationId]);

        return $startedAt === null ? null : (int) round((microtime(true) - $startedAt) * 1000);
    }

    /**
     * Forget any in-flight runs. Used between tests.
     */
    public static function flushState(): void
    {
        static::$startedAt = [];
    }
}
