<?php

use Laraplate\AI\Agents\EntityClassificationAgent;
use Laraplate\Entities\User\Models\User;
use Laraplate\Entities\User\Services\UserRiskAssessmentService;
use Laravel\Ai\Ai;

it('assesses a user and returns a structured verdict', function () {
    Ai::fakeAgent(EntityClassificationAgent::class, [[
        'label' => 'over_privileged',
        'confidence' => 0.82,
        'reason' => 'The account holds super-admin but has never signed in.',
    ]]);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $verdict = app(UserRiskAssessmentService::class)->handle($user);

    expect($verdict)->toBe([
        'label' => 'over_privileged',
        'confidence' => 0.82,
        'reason' => 'The account holds super-admin but has never signed in.',
    ]);
});

it('gives the model the account context and the role lookup tool', function () {
    Ai::fakeAgent(EntityClassificationAgent::class, [[
        'label' => 'healthy',
        'confidence' => 0.5,
        'reason' => 'Nothing stands out.',
    ]]);

    $user = User::factory()->create();

    app(UserRiskAssessmentService::class)->handle($user);

    Ai::assertAgentWasPrompted(EntityClassificationAgent::class, function ($prompt) use ($user): bool {
        $names = array_map(fn ($tool): string => $tool->name(), [...$prompt->agent->tools()]);
        $instructions = (string) $prompt->agent->instructions();

        return in_array('find_role', $names, true)
            && in_array('read_roles', $names, true)
            && str_contains($instructions, $user->{User::EMAIL})
            && str_contains($instructions, implode(', ', UserRiskAssessmentService::LABELS));
    });
});

it('constrains the schema to the labels the service accepts', function () {
    $user = User::factory()->create();

    $agent = $user->ai()->agent(EntityClassificationAgent::class, UserRiskAssessmentService::LABELS);

    $schema = $agent->schema(new Illuminate\JsonSchema\JsonSchemaTypeFactory);

    expect($schema['label']->toArray()['enum'])->toBe(UserRiskAssessmentService::LABELS);
});
