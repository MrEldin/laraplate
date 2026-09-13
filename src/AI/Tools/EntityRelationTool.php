<?php

namespace Laraplate\AI\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laraplate\AI\Contracts\HasAiContext;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Lets an agent pull one of the entity's exposed relations on demand.
 *
 * The context already carries the relations inline, but a tool keeps large
 * relations out of the instructions until the model actually needs them.
 */
class EntityRelationTool implements Tool
{
    public function __construct(
        protected Model $entity,
        protected string $exposedName,
        protected string $relation,
    ) {}

    /**
     * Get the name of the tool.
     */
    public function name(): string
    {
        return Str::of($this->exposedName)->snake()->prepend('read_')->toString();
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return sprintf(
            'Read the %s related to %s. Returns JSON. Call this when you need details you were not given.',
            $this->exposedName,
            $this->entity instanceof HasAiContext ? $this->entity->aiLabel() : class_basename($this->entity),
        );
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $related = $this->entity->{$this->relation};

        $payload = match (true) {
            $related === null => null,
            $related instanceof Model => $this->redact($related),
            default => $related->map(fn (Model $model): array => $this->redact($model))->all(),
        };

        return json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    /**
     * Reduce a related model to the attributes it allows the AI to see.
     *
     * @return array<string, mixed>
     */
    protected function redact(Model $related): array
    {
        return $related instanceof HasAiContext
            ? $related->aiAttributes()
            : $related->attributesToArray();
    }
}
