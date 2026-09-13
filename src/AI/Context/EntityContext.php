<?php

namespace Laraplate\AI\Context;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laravel\Ai\Contracts\Tool;

/**
 * An immutable, already-redacted snapshot of an entity, ready to be handed to a
 * model.
 *
 * Nothing downstream reads the Eloquent model directly: agents and tools see
 * only what the entity chose to expose, which keeps secrets such as password
 * hashes out of prompts no matter which agent is used.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class EntityContext implements Arrayable
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $relations
     * @param  list<Tool>  $tools
     */
    public function __construct(
        public Model $entity,
        public string $label,
        public string $description,
        public array $attributes,
        public array $relations = [],
        public array $tools = [],
    ) {}

    /**
     * Get the entity's class name without its namespace, e.g. "User".
     */
    public function type(): string
    {
        return class_basename($this->entity);
    }

    /**
     * Get the entity's primary key.
     */
    public function key(): int|string|null
    {
        return $this->entity->getKey();
    }

    /**
     * Return a copy carrying the given additional tools.
     *
     * @param  list<Tool>  $tools
     */
    public function withTools(array $tools): self
    {
        return new self(
            $this->entity,
            $this->label,
            $this->description,
            $this->attributes,
            $this->relations,
            [...$this->tools, ...$tools],
        );
    }

    /**
     * Render the context as the instruction block an agent prepends to its role.
     */
    public function toPrompt(): string
    {
        $sections = [
            "You are working with a single {$this->type()} record from the application's database.",
            $this->description,
            "Record: {$this->label}",
            'Attributes (JSON):',
            $this->encode($this->attributes),
        ];

        foreach ($this->relations as $name => $value) {
            $sections[] = "Related {$name} (JSON):";
            $sections[] = $this->encode($value);
        }

        return implode("\n", array_filter($sections));
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'key' => $this->key(),
            'label' => $this->label,
            'description' => $this->description,
            'attributes' => $this->attributes,
            'relations' => $this->relations,
            'tools' => (new Collection($this->tools))
                ->map(fn (Tool $tool): string => $tool::class)
                ->all(),
        ];
    }

    /**
     * Encode a value as pretty JSON the model can read reliably.
     */
    private function encode(mixed $value): string
    {
        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
