<?php

use Laraplate\AI\Context\EntityContext;
use Laraplate\AI\Context\EntityContextFactory;
use Laraplate\AI\Tools\EntityRelationTool;
use Laraplate\Entities\Permission\Models\Permission;
use Laraplate\Entities\Role\Models\Role;
use Laraplate\Entities\User\Models\User;

it('builds a context describing the entity', function () {
    $user = User::factory()->create();

    $context = $user->aiContext();

    expect($context)->toBeInstanceOf(EntityContext::class)
        ->and($context->type())->toBe('User')
        ->and($context->key())->toBe($user->getKey())
        ->and($context->label)->toContain($user->{User::EMAIL})
        ->and($context->attributes[User::EMAIL])->toBe($user->{User::EMAIL});
});

it('never exposes credential attributes to a model', function () {
    $user = User::factory()->create();

    $context = $user->aiContext();

    expect($context->attributes)
        ->not->toHaveKey('password')
        ->not->toHaveKey('remember_token');

    // The guarantee that matters is that the rendered prompt is clean, since
    // that is the string which actually reaches the provider.
    expect($context->toPrompt())
        ->not->toContain($user->getAuthPassword())
        ->not->toContain('remember_token');
});

it('redacts related entities as well as the entity itself', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $context = $user->aiContext();

    expect($context->relations)->toHaveKey('roles')
        ->and($context->relations['roles'])->toBeArray()
        ->and($context->relations['roles'][0])->toHaveKey('name');
});

it('renders a prompt carrying the description, label and attributes', function () {
    $role = Role::factory()->create([Role::NAME => 'auditor']);

    $prompt = $role->aiContext()->toPrompt();

    expect($prompt)
        ->toContain('Role')
        ->toContain('auditor')
        ->toContain('This record is a role.')
        ->toContain('Attributes (JSON):');
});

it('exposes a read tool for each declared relation', function () {
    $role = Role::factory()->create();

    $tools = $role->aiContext()->tools;

    expect($tools)->toHaveCount(1)
        ->and($tools[0])->toBeInstanceOf(EntityRelationTool::class)
        ->and($tools[0]->name())->toBe('read_permissions');
});

it('refuses entities that have not opted in', function () {
    $factory = app(EntityContextFactory::class);

    $factory->make(new class extends Illuminate\Database\Eloquent\Model {});
})->throws(RuntimeException::class, 'must implement');

it('fails loudly when an entity declares a relation it does not have', function () {
    $role = Role::factory()->create();

    $broken = new class($role->getAttributes()) extends Role
    {
        public function aiRelations(): array
        {
            return ['ghosts' => 'ghosts'];
        }
    };

    app(EntityContextFactory::class)->make($broken);
})->throws(RuntimeException::class, 'unknown AI relation');

it('reads a relation through its tool', function () {
    $role = Role::factory()->create();
    $permission = Permission::factory()->create();
    $role->permissions()->attach($permission);

    $tool = $role->fresh()->aiContext()->tools[0];

    $payload = json_decode($tool->handle(new Laravel\Ai\Tools\Request), true);

    expect($payload)->toHaveCount(1)
        ->and($payload[0]['name'])->toBe($permission->{Permission::NAME});
});
