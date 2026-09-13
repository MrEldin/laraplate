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

class RoleAttachPermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_attach_one_permission_to_one_role()
    {
        //ARRANGE
        $roleData = Role::factory()->create();
        $permission = Permission::factory()->create();

        //ACT
        $response = $this->put(
            url("/api/roles/{$roleData->{Role::ID}}/permissions/{$permission->{Permission::ID}}"),
            [],
            $this->getRequestHeaders()
        );

        $perm = $roleData->fresh()->permissions->first()->toArray();

        unset($perm['pivot']);

        //ASSERT
        $this->assertEquals($permission->toArray(), $perm);
    }
}
