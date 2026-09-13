<?php

namespace Laraplate\Entities\Permission\Models;

use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission as PermissionMainModel;

class Permission extends PermissionMainModel
{
    use HasFactory;

    const ID = 'id';
    const NAME = 'name';
    const LABEL = 'label';
    const GUARD_NAME = 'guard_name';

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): PermissionFactory
    {
        return PermissionFactory::new();
    }
}
