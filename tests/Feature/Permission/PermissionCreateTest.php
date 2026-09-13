<?php

namespace Tests\Feature\Permission;

use Laraplate\Entities\Permission\Models\Permission;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermissionCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_create_permission()
    {
        //ARRANGE
        $permissionData = Permission::factory()->make();

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
