<?php

namespace Laraplate\AI\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laraplate\AI\Contracts\HasAiContext;
use Laravel\Ai\Tools\Request;
use RuntimeException;
use Stringable;

/**
 * Lets an agent change a narrow, explicitly listed set of attributes.
 *
 * Two independent guards apply. The writable list is fixed when the tool is
 * constructed, so the model cannot reach an attribute nobody offered it; and
 * because the tool is approvable, the write still waits for a human.
 */
class EntityUpdateTool extends ApprovableTool
{
    /**
     * @param  list<string>  $writable  The only attributes this tool may change.
     */
    public function __construct(
        protected Model $entity,
        protected array $writable,
    ) {
        if ($this->writable === []) {
            throw new RuntimeException('An update tool needs at least one writable attribute.');
        }
    }

    /**
     * Get the name of the tool.
     */
    public function name(): string
    {
        return Str::of(class_basename($this->entity))->snake()->prepend('update_')->toString();
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return sprintf(
            'Update %s. You may only change: %s. Changes require human approval before they take effect.',
            $this->entity instanceof HasAiContext ? $this->entity->aiLabel() : class_basename($this->entity),
            implode(', ', $this->writable),
        );
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $changes = array_intersect_key($request->all(), array_flip($this->writable));

        if ($changes === []) {
            return 'No writable attribute was supplied, so nothing changed.';
        }

        $this->entity->forceFill($changes)->save();

        return 'Updated '.implode(', ', array_keys($changes)).'.';
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        $properties = [];

        foreach ($this->writable as $attribute) {
            $properties[$attribute] = $schema->string()
                ->description("The new value for {$attribute}. Omit to leave it unchanged.");
        }

        return $properties;
    }
}
