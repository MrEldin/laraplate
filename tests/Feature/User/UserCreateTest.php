<?php

namespace Tests\Feature\User;

use Illuminate\Http\Response;
use Laraplate\Entities\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_create_user()
    {
        //ARRANGE
        $userData = User::factory()->make()->toArray();

        $userData['password'] = 'secret';

        //ACT
        $response = $this->post(
            url('/api/users'),
            $userData,
            $this->getRequestHeaders()
        );

        //ASSERT
        unset($userData['password']);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas(User::TABLE, $userData);
    }
}
