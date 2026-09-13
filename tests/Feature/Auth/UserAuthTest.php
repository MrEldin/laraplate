<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Laraplate\Entities\User\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserAuthTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function it_should_login_a_user()
    {
        // arrange
        $user = User::factory()->create([User::PASSWORD => 'password']);

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
