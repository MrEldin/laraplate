<?php

use Illuminate\Http\Response;
use Laraplate\Api\V1\Transformers\RoleTransformer;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Entities\Role\Models\Role;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use PHPOpenSourceSaver\Fractal\Resource\Item;

it('lists every role', function () {
    Role::truncate();
    $roles = Role::factory()->count(2)->create();

    $response = $this->get(url('/api/roles'), authHeaders());

    expect($response)->toMatchTransformed(new Collection($roles, new RoleTransformer));
});

it('lists every role together with its permissions', function () {
    Role::truncate();
    $roles = Role::factory()->count(2)->create();
    $permission = Permission::factory()->create();

    $roles->each(fn (Role $role) => $role->permissions()->attach($permission));

    $response = $this->get(url('/api/roles'), authHeaders());

    expect($response)->toMatchTransformed(new Collection($roles, new RoleTransformer));
});

it('shows a single role', function () {
    $role = Role::factory()->create();

    $response = $this->get(
        url('/api/roles', [Role::ID => $role->{Role::ID}]),
        authHeaders()
    );

    expect($response)->toMatchTransformed(new Item($role, new RoleTransformer));
});

it('creates a role', function () {
    $role = Role::factory()->make();

    $this->post(url('/api/roles'), $role->only(['name']), authHeaders());

    $this->assertDatabaseHas('roles', [Role::LABEL => $role->{Role::NAME}]);
});

it('updates a role', function () {
    $role = Role::factory()->create();
    $updates = Role::factory()->make();

    $response = $this->put(
        url('/api/roles', ['id' => $role->{Role::ID}]),
        $updates->toArray(),
        authHeaders()
    );

    $response->assertStatus(Response::HTTP_OK);
    $this->assertDatabaseHas('roles', $updates->toArray());
});

it('deletes a role', function () {
    $role = Role::factory()->create();
    $other = Role::factory()->make();

    $response = $this->delete(
        url('/api/roles', ['id' => $role->{Role::ID}]),
        $other->toArray(),
        authHeaders()
    );

    $response->assertStatus(Response::HTTP_OK);
    $this->assertDatabaseMissing('roles', $other->toArray());
});

it('rejects an invalid create request', function (string $field, string $message) {
    $roleData = Role::factory()->create()->toArray();
    $roleData[$field] = '';

    $response = $this->post(url('/api/roles'), $roleData, authHeaders());

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    expect($response->getOriginalContent()['errors']->get($field)[0])->toBe($message);
})->with([
    'missing name' => [Role::NAME, 'The name field is required.'],
]);
