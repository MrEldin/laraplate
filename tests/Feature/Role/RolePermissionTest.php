<?php

use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Entities\Role\Models\Role;

/**
 * The role's permissions as a plain array, without the pivot columns that the
 * permission model itself does not carry.
 */
function attachedPermission(Role $role): array
{
    $permission = $role->fresh()->permissions->first()->toArray();

    unset($permission['pivot']);

    return $permission;
}

it('attaches a permission to a role', function () {
    $role = Role::factory()->create();
    $permission = Permission::factory()->create();

    $this->put(
        url("/api/roles/{$role->{Role::ID}}/permissions/{$permission->{Permission::ID}}"),
        [],
        authHeaders()
    );

    expect(attachedPermission($role))->toEqual($permission->toArray());
});

it('detaches a permission from a role', function () {
    $role = Role::factory()->create();
    $permission = Permission::factory()->create();

    $role->permissions()->attach($permission);

    $this->delete(
        url("/api/roles/{$role->{Role::ID}}/permissions/{$permission->{Permission::ID}}"),
        [],
        authHeaders()
    );

    expect($role->fresh()->permissions->first())->toBeNull();
});

it('attaches a permission to a role through sync', function () {
    $role = Role::factory()->create();
    $permission = Permission::factory()->create();

    $this->post(
        url("/api/roles/{$role->{Role::ID}}/permissions/{$permission->{Permission::ID}}"),
        [],
        authHeaders()
    );

    expect(attachedPermission($role))->toEqual($permission->toArray());
});

it('detaches an already attached permission through sync', function () {
    $role = Role::factory()->create();
    $permission = Permission::factory()->create();

    $role->permissions()->attach($permission);

    $this->post(
        url("/api/roles/{$role->{Role::ID}}/permissions/{$permission->{Permission::ID}}"),
        [],
        authHeaders()
    );

    expect($role->fresh()->permissions->first())->toBeNull();
});
