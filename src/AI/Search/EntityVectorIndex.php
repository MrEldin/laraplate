<?php

namespace Laraplate\AI\Search;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laraplate\AI\Models\AiEmbedding;
use Laravel\Ai\Embeddings;

/**
 * Stores one embedding per entity and ranks entities by cosine similarity.
 *
 * Similarity is computed in PHP over the candidate rows. That is deliberate:
 * it works identically on SQLite and PostgreSQL, needs no database extension,
 * and keeps the whole feature testable. It is linear in the number of indexed
 * rows, so past roughly ten thousand entities of one type you should move the
 * ranking into the database -- pgvector's `<=>` operator over a `vector` column
 * is the drop-in replacement, and only this class needs to change.
 */
class EntityVectorIndex
{
    /**
     * Index a single entity, skipping work when its text has not changed.
     */
    public function index(Model $entity): ?AiEmbedding
    {
        $content = trim((string) $entity->aiSearchableText());

        if ($content === '') {
            $this->forget($entity);

            return null;
        }

        $hash = hash('sha256', $content);

        $existing = $this->query($entity)->first();

        if ($existing !== null && $existing->content_hash === $hash) {
            return $existing;
        }

        $response = Embeddings::for([$content])->generate();

        return AiEmbedding::query()->updateOrCreate(
            [
                'embeddable_type' => $entity->getMorphClass(),
                'embeddable_id' => $entity->getKey(),
            ],
            [
                'content' => $content,
                'content_hash' => $hash,
                'provider' => $response->meta->provider ?? 'unknown',
                'model' => $response->meta->model ?? 'unknown',
                'vector' => $response->first(),
                'dimensions' => count($response->first()),
            ],
        );
    }

    /**
     * Drop an entity from the index.
     */
    public function forget(Model $entity): void
    {
        $this->query($entity)->delete();
    }

    /**
     * Find the entities of the given type most similar to the query text.
     *
     * @param  class-string<Model>  $model
     * @return Collection<int, Model>
     */
    public function search(string $model, string $query, int $limit = 5, float $minimumScore = 0.0): Collection
    {
        $scored = $this->scores($model, $query, $limit, $minimumScore);

        if ($scored->isEmpty()) {
            return new Collection;
        }

        $entities = $model::query()
            ->whereIn((new $model)->getKeyName(), $scored->keys()->all())
            ->get()
            ->keyBy(fn (Model $entity) => $entity->getKey());

        // Preserve similarity order, which the whereIn query does not.
        return $scored->keys()
            ->map(fn ($key): ?Model => $entities->get($key)?->setAttribute('ai_score', $scored->get($key)))
            ->filter()
            ->values();
    }

    /**
     * Score every indexed entity of the given type against the query.
     *
     * @param  class-string<Model>  $model
     * @return Collection<int|string, float>  Keyed by primary key, best first.
     */
    public function scores(string $model, string $query, int $limit = 5, float $minimumScore = 0.0): Collection
    {
        $query = trim($query);

        if ($query === '') {
            return new Collection;
        }

        $vector = Embeddings::for([$query])->generate()->first();

        return AiEmbedding::query()
            ->where('embeddable_type', (new $model)->getMorphClass())
            ->get()
            ->mapWithKeys(fn (AiEmbedding $row): array => [
                $row->embeddable_id => $this->cosine($vector, $row->vector),
            ])
            ->filter(fn (float $score): bool => $score >= $minimumScore)
            ->sortDesc()
            ->take($limit);
    }

    /**
     * Cosine similarity of two vectors, in [-1, 1].
     */
    public function cosine(array $a, array $b): float
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $value) {
            $other = $b[$i] ?? 0.0;

            $dot += $value * $other;
            $normA += $value * $value;
            $normB += $other * $other;
        }

        if ($normA <= 0.0 || $normB <= 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Scope a query to the row for the given entity.
     */
    protected function query(Model $entity)
    {
        return AiEmbedding::query()
            ->where('embeddable_type', $entity->getMorphClass())
            ->where('embeddable_id', $entity->getKey());
    }
}
