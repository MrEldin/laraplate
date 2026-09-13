<?php

namespace Tests\Feature\Role;

use PHPOpenSourceSaver\Fractal\Manager;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use PHPOpenSourceSaver\Fractal\Resource\Item;
use Laraplate\Api\V1\Transformers\RoleTransformer;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Entities\Role\Models\Role;
use Laraplate\Serializers\CustomSerializer;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleSyncPermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_attach_one_permission_to_one_role_throw_sync()
    {
        //ARRANGE
        $roleData = Role::factory()->create();
        $permission = Permission::factory()->create();

        //ACT
        $response = $this->post(
            url("/api/roles/{$roleData->{Role::ID}}/permissions/{$permission->{Permission::ID}}"),
            [],
            $this->getRequestHeaders()
        );

        $perm = $roleData->fresh()->permissions->first()->toArray();

        unset($perm['pivot']);

        //ASSERT
        $this->assertEquals($permission->toArray(), $perm);
    }

    #[Test]
    public function it_should_detach_one_permission_to_one_role_throw_sync()
    {
        //ARRANGE
        $roleData = Role::factory()->create();
        $permission = Permission::factory()->create();

        $roleData->permissions()->attach($permission);

        //ACT
        $response = $this->post(
            url("/api/roles/{$roleData->{Role::ID}}/permissions/{$permission->{Permission::ID}}"),
            [],
            $this->getRequestHeaders()
        );

        //ASSERT
        $this->assertEmpty($roleData->fresh()->permissions->first());
    }
}
