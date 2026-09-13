<?php

use Illuminate\Http\Response;
use Laraplate\Api\V1\Transformers\UserTransformer;
use Laraplate\Entities\User\Models\User;
use PHPOpenSourceSaver\Fractal\Resource\Item;

it('lists every user', function () {
    User::factory()->count(2)->create();

    $response = $this->get(url('/api/users'), authHeaders());

    $response->assertStatus(Response::HTTP_OK);

    // Two created here, plus the authenticated super-admin.
    expect($response->getOriginalContent())->toHaveCount(3);
});

it('shows a single user', function () {
    $user = User::factory()->create();

    $response = $this->get(
        url('/api/users', [User::ID => $user->{User::ID}]),
        authHeaders()
    );

    expect($response)->toMatchTransformed(new Item($user, new UserTransformer));
});

it('creates a user', function () {
    $userData = User::factory()->make()->toArray();
    $userData['password'] = 'secret';

    $response = $this->post(url('/api/users'), $userData, authHeaders());

    $response->assertStatus(Response::HTTP_OK);

    unset($userData['password']);
    $this->assertDatabaseHas(User::TABLE, $userData);
});

it('updates a user', function () {
    $user = User::factory()->create();
    $updates = User::factory()->make();

    $response = $this->put(
        url('/api/users', ['id' => $user->{User::ID}]),
        $updates->toArray(),
        authHeaders()
    );

    $response->assertStatus(Response::HTTP_OK);
    $this->assertDatabaseHas(User::TABLE, $updates->toArray());
});

it('rejects an invalid create request', function (string $field, string $message) {
    $userData = User::factory()->create()->toArray();
    $userData[$field] = '';

    $response = $this->post(url('/api/users'), $userData, authHeaders());

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    expect($response->getOriginalContent()['errors']->get($field)[0])->toBe($message);
})->with([
    'missing first name' => [User::FIRST_NAME, 'The first name field is required.'],
    'missing last name' => [User::LAST_NAME, 'The last name field is required.'],
    'missing password' => [User::PASSWORD, 'The password field is required.'],
]);
