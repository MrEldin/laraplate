<?php

namespace Laraplate\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use Laraplate\Entities\Role\Models\Role;

/**
 * Class RoleTransformer
 *
 * @package Tempest\Api\V1\Transformers
 */
class RoleTransformer extends TransformerAbstract
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected $availableIncludes = [
        'user',
        'permissions'
    ];

    /**
     * Transform user data
     *
     * @param Role $role
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'id' => (int)$role->{Role::ID},
            'name' => $role->{Role::NAME},
            'label' => $role->{Role::LABEL},
            'rawPermissions' => $role->permissions->pluck('id')
        ];
    }


    protected function includeUser(Role $role)
    {
        return $this->item($role->user, new UserTransformer(), false);
    }


    protected function includePermissions(Role $role)
    {
        return $this->collection($role->permissions, new PermissionTransformer(), false);
    }
}
