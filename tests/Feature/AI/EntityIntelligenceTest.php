<?php

use Laraplate\AI\Agents\EntityClassificationAgent;
use Laraplate\AI\Agents\EntityQuestionAgent;
use Laraplate\AI\Agents\EntitySummaryAgent;
use Laraplate\AI\EntityIntelligence;
use Laraplate\AI\Facades\Intelligence;
use Laraplate\AI\Tools\EntityLookupTool;
use Laraplate\Entities\Role\Models\Role;
use Laraplate\Entities\User\Models\User;
use Laravel\Ai\Ai;
use Laravel\Ai\AnonymousAgent;

it('reaches the AI layer from the entity itself', function () {
    $user = User::factory()->create();

    expect($user->ai())->toBeInstanceOf(EntityIntelligence::class);
});

it('reaches the same AI layer from the facade', function () {
    $user = User::factory()->create();

    $context = Intelligence::for($user)->context();

    expect($context->key())->toBe($user->getKey())
        ->and($context->toPrompt())->toBe($user->ai()->context()->toPrompt());
});

it('answers a question about an entity', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['The account was created today and has one role.']);

    $user = User::factory()->create();

    $response = $user->ai()->ask('Is this account new?');

    expect($response->text)->toBe('The account was created today and has one role.');

    Ai::assertAgentWasPrompted(EntityQuestionAgent::class, 'Is this account new?');
});

it('sends the entity context to the agent as instructions', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['ok']);

    $user = User::factory()->create();
    $user->ai()->ask('Summarise.');

    Ai::assertAgentWasPrompted(EntityQuestionAgent::class, function ($prompt) use ($user): bool {
        $instructions = (string) $prompt->agent->instructions();

        return str_contains($instructions, $user->{User::EMAIL})
            && str_contains($instructions, 'application user')
            && ! str_contains($instructions, $user->getAuthPassword());
    });
});

it('summarises an entity into structured output', function () {
    Ai::fakeAgent(EntitySummaryAgent::class, [[
        'headline' => 'An active super-admin account.',
        'details' => ['Has the super-admin role.'],
        'concerns' => [],
    ]]);

    $user = User::factory()->create();

    $summary = $user->ai()->summarize();

    expect($summary['headline'])->toBe('An active super-admin account.')
        ->and($summary['details'])->toBe(['Has the super-admin role.'])
        ->and($summary->toArray())->toHaveKeys(['headline', 'details', 'concerns']);
});

it('classifies an entity into one of the given labels', function () {
    Ai::fakeAgent(EntityClassificationAgent::class, [[
        'label' => 'active',
        'confidence' => 0.9,
        'reason' => 'The account holds a role and has a verified email.',
    ]]);

    $user = User::factory()->create();

    $result = $user->ai()->classify(['active', 'dormant', 'suspicious']);

    expect($result['label'])->toBe('active')
        ->and($result['confidence'])->toBe(0.9);
});

it('puts the allowed labels in the classification agent instructions', function () {
    $user = User::factory()->create();

    $agent = $user->ai()->agent(EntityClassificationAgent::class, ['active', 'dormant']);

    expect((string) $agent->instructions())->toContain('active, dormant');
});

it('refuses to classify without labels', function () {
    User::factory()->create()->ai()->classify([]);
})->throws(RuntimeException::class, 'At least one label');

it('refuses agents that are not entity agents', function () {
    User::factory()->create()->ai()->agent(stdClass::class);
})->throws(RuntimeException::class, 'must extend');

it('passes ad-hoc tools through to the agent', function () {
    $user = User::factory()->create();

    $agent = $user->ai()
        ->withTools(new EntityLookupTool(Role::class))
        ->agent(EntityQuestionAgent::class);

    $names = array_map(fn ($tool): string => $tool->name(), [...$agent->tools()]);

    expect($names)->toContain('read_roles')->toContain('find_role');
});

it('asks the same question across several entities', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['first', 'second']);

    $users = User::factory()->count(2)->create();

    $answers = Intelligence::askEach($users, 'Who is this?');

    expect($answers->map->text->all())->toBe(['first', 'second']);

    Ai::assertAgentWasPromptedTimes(EntityQuestionAgent::class, 2);
});

it('answers a question that is not about any entity', function () {
    Ai::fakeAgent(AnonymousAgent::class, ['Laraplate is a Laravel API boilerplate.']);

    $response = Intelligence::ask('What is Laraplate?');

    expect($response->text)->toBe('Laraplate is a Laravel API boilerplate.');
});

it('forwards ad-hoc tools to an entity-free agent', function () {
    Ai::fakeAgent(AnonymousAgent::class, ['ok']);

    Intelligence::ask('Who has the auditor role?', tools: [new EntityLookupTool(Role::class)]);

    Ai::assertAgentWasPrompted(AnonymousAgent::class, function ($prompt): bool {
        $names = array_map(fn ($tool): string => $tool->name(), [...$prompt->agent->tools()]);

        return $names === ['find_role'];
    });
});
