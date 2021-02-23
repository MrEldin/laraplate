<?php

namespace Tests\Feature\Permission;

use Illuminate\Http\Response;
use Laraplate\Entities\Permission\Models\Permission;
use Tests\TestCase;

class PermissionUpdateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_update_permission()
    {
        //ARRANGE
        $permissionData = factory(Permission::class)->create();
        $permissionUpdateData = factory(Permission::class)->make();

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
