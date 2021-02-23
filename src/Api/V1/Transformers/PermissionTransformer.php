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
class PermissionTransformer extends TransformerAbstract
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected $availableIncludes = [
        'user',
        'roles'
    ];

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
            'name'  => $permission->{AppPermission::NAME},
            'label' => $permission->{AppPermission::LABEL},
        ];
    }


    protected function includeUser(Permission $permission)
    {
        return $this->item($permission->user, new UserTransformer(), false);
    }


    protected function includeRoles(Permission $permission)
    {
        return $this->collection($permission->roles, new RoleTransformer(), false);
    }
}
