<?php

use Laraplate\Entities\User\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

it('logs a user in and issues a token for them', function () {
    $user = User::factory()->create([User::PASSWORD => 'password']);

    $response = $this->post('/api/login', [
        User::EMAIL => $user->{User::EMAIL},
        User::PASSWORD => 'password',
    ]);

    $token = $response->getOriginalContent()['access_token'];
    $subject = JWTAuth::setToken($token)->getPayload()->getClaims()['sub']->getValue();

    expect($subject)->toEqual($user->id);
});

it('rejects an invalid password', function () {
    $user = User::factory()->create([User::PASSWORD => 'password']);

    $response = $this->post('/api/login', [
        User::EMAIL => $user->{User::EMAIL},
        User::PASSWORD => 'not-the-password',
    ], ['Accept' => 'application/json']);

    expect($response->getStatusCode())->toBe(Symfony\Component\HttpFoundation\Response::HTTP_UNAUTHORIZED);
});
