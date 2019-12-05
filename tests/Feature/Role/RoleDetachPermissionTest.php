<?php

namespace Tests\Functional\Role;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use SmartlyJobs\Api\V1\Transformers\RoleTransformer;
use SmartlyJobs\Entities\Permission\Models\Permission;
use SmartlyJobs\Entities\Role\Models\Role;
use SmartlyJobs\Serializers\CustomSerializer;
use Tests\TestCase;

class RoleDetachPermissionTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_attach_one_permission_to_one_role()
    {
        //ARRANGE
        $roleData = factory(Role::class)->create();
        $permission = factory(Permission::class)->create();

        $roleData->permissions()->attach($permission);

        //ACT
        $response = $this->delete(
            url("/api/roles/{$roleData->{Role::ID}}/permissions/{$permission->{Permission::ID}}"),
            [],
            $this->getRequestHeaders()
        );

        //ASSERT
        $this->assertEmpty($roleData->fresh()->permissions->first());
    }
}
