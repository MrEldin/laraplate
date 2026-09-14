<?php

use Laraplate\AI\Agents\EntityQuestionAgent;
use Laraplate\AI\Models\AiInvocation;
use Laraplate\Entities\User\Models\User;
use Laravel\Ai\Ai;

/**
 * The layer is provider-agnostic: model tiers resolve against whichever
 * provider is configured, so ->cheap() and ->smart() mean the right thing on
 * DeepSeek and Kimi just as they do on Anthropic.
 */
it('works against DeepSeek', function () {
    config(['ai.providers.deepseek.key' => 'test-key']);
    Ai::fakeAgent(EntityQuestionAgent::class, ['ok']);

    $user = User::factory()->create();

    $user->ai()->using('deepseek')->cheap()->ask('Anything odd?');
    expect(AiInvocation::query()->latest('id')->first()->model)->toBe('deepseek-v4-flash');

    $user->ai()->using('deepseek')->smart()->ask('Think hard.');
    expect(AiInvocation::query()->latest('id')->first()->model)->toBe('deepseek-v4-pro');

    expect(AiInvocation::query()->latest('id')->first()->provider)->toBe('deepseek');
});

it('works against Kimi through the OpenAI-compatible driver', function () {
    config(['ai.providers.kimi' => [
        'driver' => 'openai-compatible',
        'url' => 'https://api.moonshot.ai/v1',
        'key' => 'test-key',
        'models' => ['text' => [
            'default' => 'kimi-k2-0905-preview',
            'cheapest' => 'kimi-k2-turbo-preview',
            'smartest' => 'kimi-k2-0905-preview',
        ]],
    ]]);

    Ai::fakeAgent(EntityQuestionAgent::class, ['ok']);

    $user = User::factory()->create();

    $user->ai()->using('kimi')->cheap()->ask('Anything odd?');

    $invocation = AiInvocation::query()->sole();

    expect($invocation->model)->toBe('kimi-k2-turbo-preview')
        ->and($invocation->provider)->toBe('kimi');
});

it('still redacts credentials whichever provider is used', function () {
    config(['ai.providers.deepseek.key' => 'test-key']);
    Ai::fakeAgent(EntityQuestionAgent::class, ['ok']);

    $user = User::factory()->create();
    $user->ai()->using('deepseek')->ask('Describe this account.');

    Ai::assertAgentWasPrompted(EntityQuestionAgent::class, function ($prompt) use ($user): bool {
        return ! str_contains((string) $prompt->agent->instructions(), $user->getAuthPassword());
    });
});
