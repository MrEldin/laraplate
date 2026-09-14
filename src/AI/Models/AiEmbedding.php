<?php

namespace Laraplate\AI\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * One stored vector per entity.
 *
 * @property array<int, float> $vector
 */
class AiEmbedding extends Model
{
    protected $table = 'ai_embeddings';

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'vector' => 'array',
            'dimensions' => 'integer',
        ];
    }

    /**
     * Get the entity this vector represents.
     */
    public function embeddable(): MorphTo
    {
        return $this->morphTo();
    }
}
