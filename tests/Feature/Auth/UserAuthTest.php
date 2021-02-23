<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Laraplate\Entities\User\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserAuthTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_should_login_a_user()
    {
        // arrange
        $user = factory(User::class)->create([User::PASSWORD => 'password']);

        // act
        $response = $this->post("/api/login", [
            User::EMAIL => $user->{User::EMAIL},
            User::PASSWORD => 'password'
        ]);

        // assert
        $content = $response->getOriginalContent();
        $token = $content['access_token'];

        $tokenPayload = JWTAuth::setToken($token)->getPayload();
        $tokenContent = $tokenPayload->getClaims();
        $fetchedUserIdFromToken = $tokenContent['sub']->getValue();

        $this->assertEquals($user->id, $fetchedUserIdFromToken, 'User id from token is not as expected');
    }

}
