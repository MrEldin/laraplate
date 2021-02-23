<?php

namespace Tests\Feature\User;

use Illuminate\Http\Response;
use Laraplate\Entities\User\Models\User;
use Tests\TestCase;

class UserIndexTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_get_all_users()
    {
        //ARRANGE
        factory(User::class, 2)->create();

        //ACT
        $response = $this->get(url('/api/users'), $this->getRequestHeaders());

        //ASSERT
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $response->getOriginalContent());
    }
}
