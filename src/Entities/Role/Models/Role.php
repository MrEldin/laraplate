<?php

namespace Laraplate\Entities\Role\Models;

use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laraplate\AI\Concerns\HasAi;
use Laraplate\AI\Concerns\HasAiSearch;
use Laraplate\AI\Contracts\HasAiContext;
use Spatie\Permission\Models\Role as RoleMainModel;

class Role extends RoleMainModel implements HasAiContext
{
    use HasAi, HasAiSearch, HasFactory;

    const ID = 'id';
    const NAME = 'name';
    const LABEL = 'label';
    const GUARD_NAME = 'guard_name';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
    }

    /**
     * Describe what this entity represents, in the application's own terms.
     */
    public function aiDescription(): string
    {
        return 'This record is a role. Its permissions define what a user holding it may do.';
    }

    /**
     * Get the relations a model may load, keyed by the name exposed to it.
     *
     * @return array<string, string>
     */
    public function aiRelations(): array
    {
        return ['permissions' => 'permissions'];
    }
}
