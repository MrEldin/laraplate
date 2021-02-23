<?php

namespace Laraplate\Entities\Permission\Models;

use Spatie\Permission\Models\Permission as PermissionMainModel;

class Permission extends PermissionMainModel
{
    const ID = 'id';
    const NAME = 'name';
    const LABEL = 'label';
    const GUARD_NAME = 'guard_name';
}
