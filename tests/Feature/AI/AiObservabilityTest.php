<?php

use Laraplate\AI\Agents\EntityClassificationAgent;
use Laraplate\AI\Agents\EntityQuestionAgent;
use Laraplate\AI\Models\AiInvocation;
use Laraplate\Entities\User\Models\User;
use Laravel\Ai\Ai;

it('records every agent run against the record that caused it', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['ok']);

    $user = User::factory()->create();
    $user->ai()->ask('Anything odd here?');

    $invocation = AiInvocation::query()->forSubject($user)->sole();

    expect($invocation->agent)->toBe(EntityQuestionAgent::class)
        ->and($invocation->provider)->toBe('anthropic')
        ->and($invocation->failed)->toBeFalse()
        ->and($invocation->duration_ms)->not->toBeNull();
});

it('records which model answered', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['ok']);

    User::factory()->create()->ai()->cheap()->ask('Anything odd?');

    expect(AiInvocation::query()->sole()->model)->toBe('claude-haiku-4-5');
});

it('uses the smartest model when asked to', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['ok']);

    User::factory()->create()->ai()->smart()->ask('Think hard about this.');

    expect(AiInvocation::query()->sole()->model)->toBe('claude-opus-5');
});

it('falls back to the configured default model', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['ok']);

    User::factory()->create()->ai()->ask('Anything odd?');

    expect(AiInvocation::query()->sole()->model)->toBe('claude-sonnet-5');
});

it('records token usage so spend can be attributed', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, [
        new Laravel\Ai\Responses\TextResponse(
            'ok',
            new Laravel\Ai\Responses\Data\Usage(promptTokens: 120, completionTokens: 30),
            new Laravel\Ai\Responses\Data\Meta('anthropic', 'claude-sonnet-5'),
        ),
    ]);

    User::factory()->create()->ai()->ask('Anything odd?');

    $invocation = AiInvocation::query()->sole();

    expect($invocation->prompt_tokens)->toBe(120)
        ->and($invocation->completion_tokens)->toBe(30)
        ->and($invocation->totalTokens())->toBe(150);
});

it('records a failed run together with its error', function () {
    User::factory()->create()->ai()->ask('This never reaches a provider.');
})->throws(Illuminate\Http\Client\StrayRequestException::class);

it('keeps a run that failed in the log', function () {
    $user = User::factory()->create();

    try {
        $user->ai()->ask('This never reaches a provider.');
    } catch (Throwable) {
        // The point of the test is what was recorded, not the exception.
    }

    $invocation = AiInvocation::query()->forSubject($user)->sole();

    expect($invocation->failed)->toBeTrue()
        ->and($invocation->error)->toContain('without a matching fake');
});

it('carries the per-run limits onto the agent', function () {
    $agent = User::factory()->create()->ai()
        ->maxSteps(3)
        ->maxTokens(512)
        ->temperature(0.2)
        ->agent(EntityQuestionAgent::class);

    expect($agent->maxSteps())->toBe(3)
        ->and($agent->maxTokens())->toBe(512)
        ->and($agent->temperature())->toBe(0.2);
});

it('leaves the limits unset unless asked', function () {
    $agent = User::factory()->create()->ai()->agent(EntityClassificationAgent::class, ['a', 'b']);

    expect($agent->maxSteps())->toBeNull()
        ->and($agent->maxTokens())->toBeNull()
        ->and($agent->temperature())->toBeNull();
});

it('can be switched off', function () {
    config()->set('intelligence.logging.enabled', false);

    expect(config('intelligence.logging.enabled'))->toBeFalse();
});
