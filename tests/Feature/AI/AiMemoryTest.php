<?php

use Laraplate\AI\Agents\EntityQuestionAgent;
use Laraplate\Entities\User\Models\User;
use Laravel\Ai\Ai;

it('does not remember anything by default', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['one']);

    $user = User::factory()->create();
    $user->ai()->ask('First question.');

    Ai::assertAgentWasPrompted(EntityQuestionAgent::class, function ($prompt): bool {
        return ! $prompt->agent->hasConversationParticipant();
    });
});

it('remembers the exchange once asked to', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['one', 'two']);

    $user = User::factory()->create();
    $thread = $user->ai()->remember();

    $thread->ask('Why was their payment declined?');
    $thread->ask('And what happened before that?');

    expect($thread->conversationId())->not->toBeNull();

    $this->assertDatabaseCount('agent_conversations', 1);
});

it('keeps the participant on the conversation', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['ok']);

    $user = User::factory()->create();
    $user->ai()->remember()->ask('Anything odd here?');

    Ai::assertAgentWasPrompted(EntityQuestionAgent::class, function ($prompt) use ($user): bool {
        return $prompt->agent->conversationParticipant()?->is($user) === true;
    });
});

it('lets a different participant own the conversation', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['ok']);

    $subject = User::factory()->create();
    $operator = User::factory()->create();

    $subject->ai()->remember(as: $operator)->ask('Anything odd here?');

    Ai::assertAgentWasPrompted(EntityQuestionAgent::class, function ($prompt) use ($operator): bool {
        return $prompt->agent->conversationParticipant()?->is($operator) === true;
    });
});

it('resumes a stored conversation by id', function () {
    Ai::fakeAgent(EntityQuestionAgent::class, ['one', 'two']);

    $user = User::factory()->create();

    $first = $user->ai()->remember();
    $first->ask('First.');
    $conversationId = $first->conversationId();

    // A completely separate call chain, as a later HTTP request would be.
    $resumed = $user->ai()->continueConversation($conversationId);
    $resumed->ask('Second.');

    expect($resumed->conversationId())->toBe($conversationId);

    $this->assertDatabaseCount('agent_conversations', 1);
});
