<?php

use Laraplate\AI\Agents\EntityQuestionAgent;
use Laraplate\AI\Tools\EntityUpdateTool;
use Laraplate\Entities\User\Models\User;
use Laravel\Ai\Tools\Request;

it('only exposes the attributes it was given', function () {
    $user = User::factory()->create();

    $tool = new EntityUpdateTool($user, [User::FIRST_NAME]);

    $schema = $tool->schema(new Illuminate\JsonSchema\JsonSchemaTypeFactory);

    expect(array_keys($schema))->toBe([User::FIRST_NAME])
        ->and((string) $tool->description())->toContain(User::FIRST_NAME);
});

it('refuses to be built without a writable attribute', function () {
    new EntityUpdateTool(User::factory()->create(), []);
})->throws(RuntimeException::class, 'at least one writable attribute');

it('requires approval before it will run', function () {
    $user = User::factory()->create();

    $tool = new EntityUpdateTool($user, [User::FIRST_NAME]);

    expect($tool->shouldRequestApproval(new Request([User::FIRST_NAME => 'Ana'])))->not->toBeNull();
});

it('writes only the attributes it was allowed to write', function () {
    $user = User::factory()->create([
        User::FIRST_NAME => 'Original',
        User::LAST_NAME => 'Surname',
    ]);

    $tool = new EntityUpdateTool($user, [User::FIRST_NAME]);

    $result = $tool->handle(new Request([
        User::FIRST_NAME => 'Changed',
        User::LAST_NAME => 'AlsoChanged',   // not writable -- must be ignored
    ]));

    $user->refresh();

    expect($user->{User::FIRST_NAME})->toBe('Changed')
        ->and($user->{User::LAST_NAME})->toBe('Surname')
        ->and((string) $result)->toContain(User::FIRST_NAME);
});

it('reports when nothing writable was supplied', function () {
    $user = User::factory()->create([User::FIRST_NAME => 'Original']);

    $result = (new EntityUpdateTool($user, [User::FIRST_NAME]))
        ->handle(new Request([User::LAST_NAME => 'Ignored']));

    expect((string) $result)->toContain('nothing changed')
        ->and($user->refresh()->{User::FIRST_NAME})->toBe('Original');
});

it('can be waived for a trusted call', function () {
    $tool = (new EntityUpdateTool(User::factory()->create(), [User::FIRST_NAME]))->withoutApproval();

    expect($tool->shouldRequestApproval(new Request([User::FIRST_NAME => 'Ana'])))->toBeNull();
});

it('reaches an agent as a callable tool', function () {
    $user = User::factory()->create();

    $agent = $user->ai()
        ->withTools(new EntityUpdateTool($user, [User::FIRST_NAME]))
        ->agent(EntityQuestionAgent::class);

    $names = array_map(fn ($tool): string => $tool->name(), [...$agent->tools()]);

    expect($names)->toContain('update_user');
});
