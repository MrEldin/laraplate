<?php

namespace Laraplate\AI\Contracts;

use Laraplate\AI\Context\EntityContext;
use Laraplate\AI\EntityIntelligence;

/**
 * Implemented by any entity that can be handed to the AI layer.
 *
 * The Laraplate\AI\Concerns\HasAi trait provides a sensible implementation of
 * every method, so an entity normally only overrides what it wants to change.
 */
interface HasAiContext
{
    /**
     * Get the fluent AI entry point for this entity.
     */
    public function ai(): EntityIntelligence;

    /**
     * Get the context describing this entity to a model.
     */
    public function aiContext(): EntityContext;

    /**
     * Get a short human readable label, e.g. "User #14".
     */
    public function aiLabel(): string;

    /**
     * Describe what this entity represents, in the application's own terms.
     */
    public function aiDescription(): string;

    /**
     * Get the attributes that may be shown to a model.
     *
     * @return array<string, mixed>
     */
    public function aiAttributes(): array;

    /**
     * Get the attribute names that must never reach a model.
     *
     * @return list<string>
     */
    public function aiRedactedAttributes(): array;

    /**
     * Get the relations a model may load, keyed by the name exposed to it.
     *
     * @return array<string, string>
     */
    public function aiRelations(): array;

    /**
     * Get extra tools that agents working on this entity may call.
     *
     * @return list<\Laravel\Ai\Contracts\Tool>
     */
    public function aiTools(): array;
}
