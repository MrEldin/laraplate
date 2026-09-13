<?php

namespace Tests\Feature\User;

use Illuminate\Http\Response;
use Laraplate\Entities\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_update_user()
    {
        //ARRANGE
        $userData = User::factory()->create();
        $userUpdateData = User::factory()->make();

        //ACT
        $response = $this->put(
            url('/api/users', ['id' => $userData->{User::ID}]),
            $userUpdateData->toArray(),
            $this->getRequestHeaders()
        );

        //ASSERT
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas(User::TABLE, $userUpdateData->toArray());
    }

}
