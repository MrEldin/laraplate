<?php

namespace Tests\Functional\Role;

use SmartlyJobs\Entities\Role\Models\Role;
use Tests\TestCase;

class RoleCreateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_create_role()
    {
        //ARRANGE
        $roleData = factory(Role::class)->make();

        //ACT
        $response = $this->post(
            url('/api/roles'),
            $roleData->only(['name']),
            $this->getRequestHeaders()
        );

        //ASSERT
        $this->assertDatabaseHas('roles', [Role::LABEL => $roleData->{Role::NAME}]);
    }
}
