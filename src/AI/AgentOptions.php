<?php

namespace Laraplate\AI;

use Laravel\Ai\Contracts\Providers\TextProvider;

/**
 * Per-run generation settings.
 *
 * The SDK reads maxSteps/maxTokens/temperature from methods on the agent before
 * falling back to class attributes, so these travel with the agent instance and
 * can be chosen at the call site instead of being baked into the class.
 */
final readonly class AgentOptions
{
    public const TIER_CHEAPEST = 'cheapest';

    public const TIER_SMARTEST = 'smartest';

    public function __construct(
        public ?string $modelTier = null,
        public ?int $maxSteps = null,
        public ?int $maxTokens = null,
        public ?float $temperature = null,
    ) {}

    /**
     * Return a copy with the given values replaced.
     */
    public function with(
        ?string $modelTier = null,
        ?int $maxSteps = null,
        ?int $maxTokens = null,
        ?float $temperature = null,
    ): self {
        return new self(
            $modelTier ?? $this->modelTier,
            $maxSteps ?? $this->maxSteps,
            $maxTokens ?? $this->maxTokens,
            $temperature ?? $this->temperature,
        );
    }

    /**
     * Resolve the model name for this run, or null to use the provider default.
     *
     * Tiers are resolved against the provider rather than hard-coded, so the
     * same call picks the right model on Anthropic, OpenAI or Gemini.
     */
    public function resolveModel(TextProvider $provider): ?string
    {
        return match ($this->modelTier) {
            self::TIER_CHEAPEST => $provider->cheapestTextModel(),
            self::TIER_SMARTEST => $provider->smartestTextModel(),
            default => null,
        };
    }
}
