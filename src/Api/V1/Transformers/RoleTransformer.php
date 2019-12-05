<?php

namespace SmartlyJobs\Api\V1\Transformers;

use League\Fractal\TransformerAbstract;
use SmartlyJobs\Entities\Role\Models\Role;

/**
 * @SWG\Definition (
 *      definition="RoleTransformerV1",
 *      required={},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="subtitle",
 *          description="subtitle",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="active",
 *          description="active",
 *          type="string"
 *      )
 * )
 *
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
