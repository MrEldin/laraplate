<?php

namespace Laraplate\AI\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laraplate\AI\Contracts\HasAiContext;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use RuntimeException;
use Stringable;

/**
 * Lets an agent search entities by meaning.
 *
 * This is what turns the vector index into something an agent can use on its
 * own: it can look for records that resemble a description rather than being
 * handed a fixed list up front.
 */
class EntitySearchTool implements Tool
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(
        protected string $model,
        protected int $limit = 5,
    ) {
        if (! method_exists($this->model, 'aiSearch')) {
            throw new RuntimeException(
                "[{$this->model}] is not searchable. Add the ".\Laraplate\AI\Concerns\HasAiSearch::class.' trait.'
            );
        }
    }

    /**
     * Get the name of the tool.
     */
    public function name(): string
    {
        return Str::of(class_basename($this->model))->snake()->plural()->prepend('search_')->toString();
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return sprintf(
            'Search %s records by meaning, not by exact wording. Describe what you are looking for in plain language. Returns JSON, best matches first.',
            class_basename($this->model),
        );
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        ['query' => $query] = $request->validate(['query' => ['required', 'string']]);

        $results = $this->model::aiSearch($query, $this->limit)
            ->map(fn (Model $entity): array => [
                'id' => $entity->getKey(),
                'score' => round((float) $entity->getAttribute('ai_score'), 4),
                'attributes' => $entity instanceof HasAiContext
                    ? $entity->aiAttributes()
                    : $entity->attributesToArray(),
            ])
            ->all();

        return json_encode($results, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()
                ->description('What to look for, described in plain language.')
                ->required(),
        ];
    }
}
