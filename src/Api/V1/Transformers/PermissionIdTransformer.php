<?php

namespace Laraplate\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use Laraplate\Entities\Permission\Models\Permission as AppPermission;
use Spatie\Permission\Models\Permission;

/**
 * Class PermissionTransformer
 *
 * @package Tempest\Api\V1\Transformers
 */
class PermissionIdTransformer extends TransformerAbstract
{

    /**
     * Transform user data
     *
     * @param Permission $permission
     * @return array
     */
    public function transform(Permission $permission)
    {
        return [
            'id'    => (int)$permission->{AppPermission::ID},
        ];
    }
}
