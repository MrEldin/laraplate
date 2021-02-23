<?php

namespace Tests\Feature\Permission;

use Laraplate\Entities\Permission\Models\Permission;
use Tests\TestCase;

class PermissionCreateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_create_permission()
    {
        //ARRANGE
        $permissionData = factory(Permission::class)->make();

        //ACT
        $response = $this->post(
            url('/api/permissions'),
            $permissionData->toArray(),
            $this->getRequestHeaders()
        );

        //ASSERT
        $this->assertDatabaseHas('permissions', $permissionData->toArray());
    }
}
