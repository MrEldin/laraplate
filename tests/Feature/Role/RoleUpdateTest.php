<?php

namespace Tests\Feature\Role;

use Illuminate\Http\Response;
use Laraplate\Entities\Role\Models\Role;
use Tests\TestCase;

class RoleUpdateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_update_role()
    {
        //ARRANGE
        $roleData = factory(Role::class)->create();
        $roleUpdateData = factory(Role::class)->make();

        //ACT
        $response = $this->put(
            url('/api/roles', ['id' => $roleData->{Role::ID}]),
            $roleUpdateData->toArray(),
            $this->getRequestHeaders()
        );

        //ASSERT
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('roles', $roleUpdateData->toArray());
    }

}
