<?php

namespace Laraplate\AI\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A record of one agent run: which agent, on which entity, at what token cost.
 *
 * @property int $prompt_tokens
 * @property int $completion_tokens
 */
class AiInvocation extends Model
{
    protected $table = 'ai_invocations';

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'failed' => 'boolean',
        ];
    }

    /**
     * Get the entity the run was about, if any.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Total tokens billed for this run.
     */
    public function totalTokens(): int
    {
        return $this->prompt_tokens
            + $this->completion_tokens
            + $this->cache_read_tokens
            + $this->cache_write_tokens;
    }

    /**
     * Scope the query to runs about the given entity.
     */
    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query
            ->where('subject_type', $subject->getMorphClass())
            ->where('subject_id', $subject->getKey());
    }
}
