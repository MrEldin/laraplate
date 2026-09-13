<?php

namespace Laraplate\AI\Context;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Laraplate\AI\Contracts\HasAiContext;
use Laraplate\AI\Tools\EntityRelationTool;
use RuntimeException;

/**
 * Builds an EntityContext from an entity, applying the redaction and relation
 * rules the entity declared.
 */
class EntityContextFactory
{
    /**
     * Build the context for the given entity.
     */
    public function make(Model $entity): EntityContext
    {
        $this->ensureSupported($entity);

        return new EntityContext(
            entity: $entity,
            label: $entity->aiLabel(),
            description: $entity->aiDescription(),
            attributes: $entity->aiAttributes(),
            relations: $this->resolveRelations($entity),
            tools: [...$this->relationTools($entity), ...$entity->aiTools()],
        );
    }

    /**
     * Eagerly resolve the relations the entity exposes.
     *
     * Relations are read through the entity's own aiAttributes() where the
     * related model also opts in, so redaction applies transitively.
     *
     * @return array<string, mixed>
     */
    protected function resolveRelations(Model $entity): array
    {
        return Arr::map(
            $entity->aiRelations(),
            fn (string $relation): mixed => $this->readRelation($entity, $relation)
        );
    }

    /**
     * Read a single relation as a redacted array.
     */
    protected function readRelation(Model $entity, string $relation): mixed
    {
        if (! method_exists($entity, $relation) || ! $entity->{$relation}() instanceof Relation) {
            throw new RuntimeException(
                'Entity ['.$entity::class.'] exposes unknown AI relation ['.$relation.'].'
            );
        }

        $related = $entity->{$relation};

        return match (true) {
            $related === null => null,
            $related instanceof Model => $this->redact($related),
            default => $related->map(fn (Model $model): array => $this->redact($model))->all(),
        };
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

    /**
     * Build a read tool for each exposed relation.
     *
     * @return list<EntityRelationTool>
     */
    protected function relationTools(Model $entity): array
    {
        $tools = [];

        foreach ($entity->aiRelations() as $exposedName => $relation) {
            $tools[] = new EntityRelationTool($entity, $exposedName, $relation);
        }

        return $tools;
    }

    /**
     * Fail loudly when an entity has not opted into the AI layer.
     */
    protected function ensureSupported(Model $entity): void
    {
        if (! $entity instanceof HasAiContext) {
            throw new RuntimeException(
                'Entity ['.$entity::class.'] must implement '.HasAiContext::class.
                ' to be used with the AI layer. Add the '.\Laraplate\AI\Concerns\HasAi::class.' trait.'
            );
        }
    }
}
