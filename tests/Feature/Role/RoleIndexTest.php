<?php

namespace Tests\Functional\Role;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use SmartlyJobs\Api\V1\Transformers\RoleTransformer;
use SmartlyJobs\Entities\Permission\Models\Permission;
use SmartlyJobs\Entities\Role\Models\Role;
use SmartlyJobs\Serializers\CustomSerializer;
use Tests\TestCase;

class RoleIndexTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_get_all_roles()
    {
        //ARRANGE
        Role::truncate();
        $roleData = factory(Role::class, 2)->create();

        //ACT
        $response = $this->get(
            url('/api/roles'),
            $this->getRequestHeaders()
        );

        $manager = new Manager;
        $manager->setSerializer(new CustomSerializer);
        $resource = new Collection($roleData, new RoleTransformer());

        //ASSERT
        $this->assertEquals($manager->createData($resource)->toJson(), $response->getContent());
    }

    /** @test */
    public function it_should_get_all_roles_with_permissions()
    {
        //ARRANGE
        Role::truncate();
        $roleData = factory(Role::class, 2)->create();
        $permission = factory(Permission::class)->create();
        foreach ($roleData as $role) {
            $role->permissions()->attach($permission);
        }

        //ACT
        $response = $this->get(
            url('/api/roles'),
            $this->getRequestHeaders()
        );

        $manager = new Manager;
        $manager->setSerializer(new CustomSerializer);
        $resource = new Collection($roleData, new RoleTransformer());

        //ASSERT
        $this->assertEquals($manager->createData($resource)->toJson(), $response->getContent());
    }
}
