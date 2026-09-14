<?php

namespace Laraplate\AI\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Closure;
use Throwable;
use Laraplate\AI\Models\AiEmbedding;
use Laraplate\AI\Search\EntityVectorIndex;

/**
 * Makes an entity searchable by meaning rather than by string matching.
 *
 *     class Invoice extends Model implements HasAiContext
 *     {
 *         use HasAi, HasAiSearch;
 *     }
 *
 *     Invoice::aiSearch('unpaid invoices from German customers')->take(5);
 *
 * Entities are re-indexed when saved and dropped from the index when deleted.
 * Indexing costs an embeddings call, so it is skipped when the searchable text
 * is unchanged.
 *
 * Requires the HasAi trait, whose aiAttributes() supplies the default text.
 */
trait HasAiSearch
{
    /**
     * Set while indexing is suppressed for the current process.
     */
    protected static bool $aiIndexingDisabled = false;

    /**
     * Keep the index in step with the entity.
     */
    public static function bootHasAiSearch(): void
    {
        static::saved(function (Model $entity): void {
            if (! $entity::aiIndexesAutomatically()) {
                return;
            }

            // Indexing costs a call to the embeddings provider. A provider
            // outage must degrade search, never reject a write that already
            // succeeded, so failures here are logged rather than thrown.
            try {
                $entity->aiIndex();
            } catch (Throwable $e) {
                Log::warning('Failed to index '.$entity::class.' ['.$entity->getKey().'] for AI search.', [
                    'exception' => $e->getMessage(),
                ]);
            }
        });

        static::deleted(fn (Model $entity) => $entity->aiForget());
    }

    /**
     * Whether saving an entity should re-index it.
     *
     * Override and return false when you would rather index from a job or from
     * the ai:index command, which is the better choice for bulk imports.
     */
    public static function aiIndexesAutomatically(): bool
    {
        return ! static::$aiIndexingDisabled
            && (bool) config('intelligence.search.auto_index', true);
    }

    /**
     * Run a callback with automatic indexing switched off.
     *
     * Use it around imports and back-fills, then rebuild once with ai:index.
     */
    public static function withoutAiIndexing(Closure $callback): mixed
    {
        $previous = static::$aiIndexingDisabled;

        static::$aiIndexingDisabled = true;

        try {
            return $callback();
        } finally {
            static::$aiIndexingDisabled = $previous;
        }
    }

    /**
     * The text that represents this entity for search.
     *
     * Defaults to the same redacted attributes the AI layer shows a model, so
     * nothing reaches the embeddings provider that an agent could not see.
     */
    public function aiSearchableText(): string
    {
        $parts = [];

        foreach ($this->aiAttributes() as $attribute => $value) {
            if ($value === null || $value === '' || is_array($value)) {
                continue;
            }

            $parts[] = "{$attribute}: {$value}";
        }

        return implode("\n", $parts);
    }

    /**
     * Add or refresh this entity in the index.
     */
    public function aiIndex(): ?AiEmbedding
    {
        return app(EntityVectorIndex::class)->index($this);
    }

    /**
     * Remove this entity from the index.
     */
    public function aiForget(): void
    {
        app(EntityVectorIndex::class)->forget($this);
    }

    /**
     * Search entities of this type by meaning.
     *
     * @return Collection<int, static>
     */
    public static function aiSearch(string $query, int $limit = 5, float $minimumScore = 0.0): Collection
    {
        return app(EntityVectorIndex::class)->search(static::class, $query, $limit, $minimumScore);
    }

    /**
     * Get the stored embedding for this entity.
     */
    public function aiEmbedding(): MorphOne
    {
        return $this->morphOne(AiEmbedding::class, 'embeddable');
    }
}
