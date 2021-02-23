<?php

namespace Laraplate\Entities\Role\Models;

use Spatie\Permission\Models\Role as RoleMainModel;

class Role extends RoleMainModel
{
    const ID = 'id';
    const NAME = 'name';
    const LABEL = 'label';
    const GUARD_NAME = 'guard_name';
}
