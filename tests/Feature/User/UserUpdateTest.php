<?php

namespace Tests\Feature\User;

use Illuminate\Http\Response;
use Laraplate\Entities\User\Models\User;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_update_user()
    {
        //ARRANGE
        $userData = factory(User::class)->create();
        $userUpdateData = factory(User::class)->make();

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
