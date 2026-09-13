<?php

namespace Tests\Feature\Permission;

use Illuminate\Http\Response;
use Laraplate\Entities\Permission\Models\Permission;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermissionUpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_update_permission()
    {
        //ARRANGE
        $permissionData = Permission::factory()->create();
        $permissionUpdateData = Permission::factory()->make();

        //ACT
        $response = $this->put(
            url('/api/permissions', ['id' => $permissionData->{Permission::ID}]),
            $permissionUpdateData->toArray(),
            $this->getRequestHeaders()
        );

        //ASSERT
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('permissions', $permissionUpdateData->toArray());
    }

}
