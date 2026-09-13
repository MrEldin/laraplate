<?php

use Illuminate\Support\Facades\Queue;
use Laraplate\AI\Agents\EntityQuestionAgent;
use Laraplate\Entities\User\Models\User;
use Laravel\Ai\Ai;
use Laravel\Ai\Streaming\Events\TextDelta;

it('queues a question about an entity', function () {
    Queue::fake();
    Ai::fakeAgent(EntityQuestionAgent::class, ['queued']);

    User::factory()->create()->ai()->queue('Is this account new?');

    Ai::assertAgentWasQueued(EntityQuestionAgent::class, 'Is this account new?');
});

it('streams a question about an entity', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['streamed answer']);

    $stream = User::factory()->create()->ai()->stream('Describe this account.');

    $events = iterator_to_array($stream, preserve_keys: false);

    expect(TextDelta::combine($events))->toBe('streamed answer');
});

it('never prompts an agent that a test did not fake', function () {
    User::factory()->create()->ai()->ask('This should not reach a provider.');
})->throws(
    Illuminate\Http\Client\StrayRequestException::class,
    'Attempted request to [https://api.anthropic.com/v1/messages] without a matching fake.',
);
