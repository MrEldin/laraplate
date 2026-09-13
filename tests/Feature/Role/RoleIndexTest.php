<?php

namespace Tests\Feature\Role;

use PHPOpenSourceSaver\Fractal\Manager;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use Laraplate\Api\V1\Transformers\RoleTransformer;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Entities\Role\Models\Role;
use Laraplate\Serializers\CustomSerializer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_get_all_roles()
    {
        //ARRANGE
        Role::truncate();
        $roleData = Role::factory()->count(2)->create();

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

    #[Test]
    public function it_should_get_all_roles_with_permissions()
    {
        //ARRANGE
        Role::truncate();
        $roleData = Role::factory()->count(2)->create();
        $permission = Permission::factory()->create();
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
