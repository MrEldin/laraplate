<?php

use Laraplate\AI\Tools\EntityLookupTool;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Entities\Role\Models\Role;
use Laraplate\Entities\User\Models\User;
use Laravel\Ai\Tools\Request;

it('names itself after the model it reads', function () {
    expect((new EntityLookupTool(Role::class))->name())->toBe('find_role')
        ->and((new EntityLookupTool(Permission::class))->name())->toBe('find_permission');
});

it('rejects a class that is not an eloquent model', function () {
    new EntityLookupTool(stdClass::class);
})->throws(RuntimeException::class, 'is not an Eloquent model');

it('looks a record up by id', function () {
    $role = Role::factory()->create();

    $result = (new EntityLookupTool(Role::class))->handle(new Request(['id' => $role->getKey()]));

    expect(json_decode($result, true)['name'])->toBe($role->{Role::NAME});
});

it('reports a missing record rather than failing', function () {
    $result = (new EntityLookupTool(Role::class))->handle(new Request(['id' => 99999]));

    expect($result)->toBe('null');
});

it('validates the id it is given', function () {
    (new EntityLookupTool(Role::class))->handle(new Request(['id' => 'not-an-id']));
})->throws(Illuminate\Validation\ValidationException::class);

it('redacts credentials when looking up a user', function () {
    $user = User::factory()->create();

    $result = (new EntityLookupTool(User::class))->handle(new Request(['id' => $user->getKey()]));

    expect(json_decode($result, true))
        ->not->toHaveKey('password')
        ->and($result)->not->toContain($user->getAuthPassword());
});

it('describes itself to the model', function () {
    $tool = new EntityLookupTool(Role::class);

    expect((string) $tool->description())->toContain('Role')
        ->and($tool->schema(new Illuminate\JsonSchema\JsonSchemaTypeFactory))->toHaveKey('id');
});
