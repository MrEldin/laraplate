<?php

namespace Laraplate\Entities\Role\Models;

use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as RoleMainModel;

class Role extends RoleMainModel
{
    use HasFactory;

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
}
