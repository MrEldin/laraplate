<?php

namespace Tests\Feature\Role;

use Laraplate\Entities\Role\Models\Role;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_create_role()
    {
        //ARRANGE
        $roleData = Role::factory()->make();

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
