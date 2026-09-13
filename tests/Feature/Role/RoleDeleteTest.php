<?php

namespace Tests\Feature\Role;

use Illuminate\Http\Response;
use Laraplate\Entities\Role\Models\Role;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_delete_role()
    {
        //ARRANGE
        $roleData = Role::factory()->create();
        $roleUpdateData = Role::factory()->make();

        //ACT
        $response = $this->delete(
            url('/api/roles', ['id' => $roleData->{Role::ID}]),
            $roleUpdateData->toArray(),
            $this->getRequestHeaders()
        );

        //ASSERT
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('roles', $roleUpdateData->toArray());
    }

}
