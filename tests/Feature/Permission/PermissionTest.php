<?php

use Illuminate\Http\Response;
use Laraplate\Api\V1\Transformers\PermissionTransformer;
use Laraplate\Entities\Permission\Models\Permission;
use PHPOpenSourceSaver\Fractal\Resource\Collection;
use PHPOpenSourceSaver\Fractal\Resource\Item;

it('lists every permission', function () {
    Permission::truncate();
    $permissions = Permission::factory()->count(2)->create();

    $response = $this->get(url('/api/permissions'), authHeaders());

    expect($response)->toMatchTransformed(new Collection($permissions, new PermissionTransformer));
});

it('shows a single permission', function () {
    $permission = Permission::factory()->create();

    $response = $this->get(
        url('/api/permissions', [Permission::ID => $permission->{Permission::ID}]),
        authHeaders()
    );

    expect($response)->toMatchTransformed(new Item($permission, new PermissionTransformer));
});

it('creates a permission', function () {
    $permission = Permission::factory()->make();

    $this->post(url('/api/permissions'), $permission->toArray(), authHeaders());

    $this->assertDatabaseHas('permissions', $permission->toArray());
});

it('updates a permission', function () {
    $permission = Permission::factory()->create();
    $updates = Permission::factory()->make();

    $response = $this->put(
        url('/api/permissions', ['id' => $permission->{Permission::ID}]),
        $updates->toArray(),
        authHeaders()
    );

    $response->assertStatus(Response::HTTP_OK);
    $this->assertDatabaseHas('permissions', $updates->toArray());
});

it('rejects an invalid create request', function (string $field, string $message) {
    $permissionData = Permission::factory()->create()->toArray();
    $permissionData[$field] = '';

    $response = $this->post(url('/api/permissions'), $permissionData, authHeaders());

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    expect($response->getOriginalContent()['errors']->get($field)[0])->toBe($message);
})->with([
    'missing name' => [Permission::NAME, 'The name field is required.'],
]);
