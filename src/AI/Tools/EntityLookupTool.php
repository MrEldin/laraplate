<?php

namespace Laraplate\AI\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laraplate\AI\Contracts\HasAiContext;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;
use RuntimeException;

/**
 * Lets an agent look up another record of the same entity type by primary key.
 *
 * Attach it deliberately -- it widens what an agent can read from one record to
 * the whole table, so it belongs on comparison or triage agents, not on every
 * agent by default.
 */
class EntityLookupTool implements Tool
{
    /**
     * @param  class-string<Model>  $model
     */
    public function __construct(protected string $model)
    {
        if (! is_subclass_of($this->model, Model::class)) {
            throw new RuntimeException("[{$this->model}] is not an Eloquent model.");
        }
    }

    /**
     * Get the name of the tool.
     */
    public function name(): string
    {
        return Str::of(class_basename($this->model))->snake()->prepend('find_')->toString();
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return sprintf(
            'Look up a single %s by its numeric id. Returns JSON, or "null" when no such record exists.',
            class_basename($this->model),
        );
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        ['id' => $id] = $request->validate(['id' => ['required', 'integer']]);

        $found = $this->model::query()->find($id);

        if ($found === null) {
            return 'null';
        }

        return json_encode(
            $found instanceof HasAiContext ? $found->aiAttributes() : $found->attributesToArray(),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The primary key of the record to read.')->required(),
        ];
    }
}
