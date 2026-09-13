<?php

namespace Laraplate\AI\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laraplate\AI\Context\EntityContext;
use Laraplate\AI\EntityIntelligence;
use Laraplate\AI\Facades\Intelligence;

/**
 * Gives an entity a first-class AI surface.
 *
 *     class User extends Model implements HasAiContext
 *     {
 *         use HasAi;
 *     }
 *
 *     $user->ai()->ask('Does this account look abandoned?');
 *     $user->ai()->summarize();
 *
 * Every method below is a default, not a rule: an entity overrides
 * aiDescription(), aiRelations() or aiTools() to teach the model more about
 * itself without touching the AI layer.
 */
trait HasAi
{
    /**
     * Get the fluent AI entry point for this entity.
     */
    public function ai(): EntityIntelligence
    {
        return Intelligence::for($this);
    }

    /**
     * Get the context describing this entity to a model.
     */
    public function aiContext(): EntityContext
    {
        return Intelligence::contextFor($this);
    }

    /**
     * Get a short human readable label, e.g. "User #14".
     */
    public function aiLabel(): string
    {
        return class_basename($this).' #'.($this->getKey() ?? 'unsaved');
    }

    /**
     * Describe what this entity represents, in the application's own terms.
     */
    public function aiDescription(): string
    {
        return Str::of(class_basename($this))
            ->headline()
            ->lower()
            ->prepend('This record represents a ')
            ->append(' in the application.')
            ->toString();
    }

    /**
     * Get the attributes that may be shown to a model.
     *
     * Redacted attributes are removed here rather than at the call site, so an
     * agent cannot reach them regardless of how it was constructed.
     *
     * @return array<string, mixed>
     */
    public function aiAttributes(): array
    {
        return Arr::except($this->attributesToArray(), $this->aiRedactedAttributes());
    }

    /**
     * Get the attribute names that must never reach a model.
     *
     * Defaults to the model's hidden attributes plus the usual credential
     * columns, which are hidden on some models and not on others.
     *
     * @return list<string>
     */
    public function aiRedactedAttributes(): array
    {
        return array_values(array_unique([
            ...$this->getHidden(),
            'password',
            'remember_token',
            'api_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
        ]));
    }

    /**
     * Get the relations a model may load, keyed by the name exposed to it.
     *
     * @return array<string, string>
     */
    public function aiRelations(): array
    {
        return [];
    }

    /**
     * Get extra tools that agents working on this entity may call.
     *
     * @return list<\Laravel\Ai\Contracts\Tool>
     */
    public function aiTools(): array
    {
        return [];
    }
}
