<?php

use Laraplate\AI\Models\AiEmbedding;
use Laraplate\AI\Search\EntityVectorIndex;
use Laraplate\AI\Tools\EntitySearchTool;
use Laraplate\Entities\Role\Models\Role;
use Laraplate\Entities\User\Models\User;
use Laravel\Ai\Embeddings;
use Laravel\Ai\Prompts\EmbeddingsPrompt;
use Laravel\Ai\Tools\Request;

/**
 * Fake embeddings that place each text at a known point, so similarity is
 * deterministic instead of depending on a provider.
 */
function fakeVectors(array $byText): void
{
    Embeddings::fake(function (EmbeddingsPrompt $prompt) use ($byText): array {
        return array_map(
            fn (string $text): array => $byText[$text] ?? [0.0, 0.0, 1.0],
            $prompt->inputs,
        );
    });
}

it('computes cosine similarity', function () {
    $index = app(EntityVectorIndex::class);

    expect($index->cosine([1, 0], [1, 0]))->toBe(1.0)
        ->and($index->cosine([1, 0], [0, 1]))->toBe(0.0)
        ->and(round($index->cosine([1, 0], [-1, 0]), 6))->toBe(-1.0)
        ->and($index->cosine([0, 0], [1, 0]))->toBe(0.0);
});

it('indexes an entity and stores its vector', function () {
    Embeddings::fake([[[0.1, 0.2, 0.3]]]);

    $role = Role::factory()->create();
    $embedding = $role->aiIndex();

    expect($embedding)->toBeInstanceOf(AiEmbedding::class)
        ->and($embedding->vector)->toBe([0.1, 0.2, 0.3])
        ->and($embedding->dimensions)->toBe(3)
        ->and($embedding->content)->toContain($role->{Role::NAME});
});

it('skips re-embedding when the text has not changed', function () {
    Embeddings::fake([[[0.1, 0.2, 0.3]], [[0.9, 0.9, 0.9]]]);

    $role = Role::factory()->create();

    $first = $role->aiIndex();
    $second = $role->aiIndex();

    expect($second->id)->toBe($first->id)
        ->and($second->vector)->toBe([0.1, 0.2, 0.3]);

    $this->assertDatabaseCount('ai_embeddings', 1);
});

it('ranks entities by meaning, closest first', function () {
    $auditor = Role::factory()->create([Role::NAME => 'auditor']);
    $courier = Role::factory()->create([Role::NAME => 'courier']);

    fakeVectors([
        $auditor->aiSearchableText() => [1.0, 0.0, 0.0],
        $courier->aiSearchableText() => [0.0, 1.0, 0.0],
        'someone who reviews the books' => [0.95, 0.05, 0.0],
    ]);

    $auditor->aiIndex();
    $courier->aiIndex();

    $results = Role::aiSearch('someone who reviews the books', limit: 2);

    expect($results->first()->getKey())->toBe($auditor->getKey())
        ->and($results->first()->getAttribute('ai_score'))->toBeGreaterThan(0.9)
        ->and($results->last()->getKey())->toBe($courier->getKey());
});

it('drops results below the minimum score', function () {
    $courier = Role::factory()->create([Role::NAME => 'courier']);

    fakeVectors([
        $courier->aiSearchableText() => [0.0, 1.0, 0.0],
        'accounting' => [1.0, 0.0, 0.0],
    ]);

    $courier->aiIndex();

    expect(Role::aiSearch('accounting', minimumScore: 0.5))->toBeEmpty();
});

it('removes an entity from the index when it is deleted', function () {
    Embeddings::fake([[[0.1, 0.2, 0.3]]]);

    $role = Role::factory()->create();
    $role->aiIndex();

    $this->assertDatabaseCount('ai_embeddings', 1);

    $role->delete();

    $this->assertDatabaseCount('ai_embeddings', 0);
});

it('never sends credentials to the embeddings provider', function () {
    $user = User::factory()->create();

    expect($user->aiSearchableText())
        ->not->toContain($user->getAuthPassword())
        ->not->toContain('password');
});

it('lets an agent search through a tool', function () {
    $auditor = Role::factory()->create([Role::NAME => 'auditor']);

    fakeVectors([
        $auditor->aiSearchableText() => [1.0, 0.0, 0.0],
        'reviews the books' => [1.0, 0.0, 0.0],
    ]);

    $auditor->aiIndex();

    $tool = new EntitySearchTool(Role::class);
    $payload = json_decode($tool->handle(new Request(['query' => 'reviews the books'])), true);

    expect($tool->name())->toBe('search_roles')
        ->and($payload[0]['id'])->toBe($auditor->getKey())
        ->and($payload[0]['attributes']['name'])->toBe('auditor');
});

it('refuses to search an entity that is not searchable', function () {
    new EntitySearchTool(Laraplate\Entities\Permission\Models\Permission::class);
})->throws(RuntimeException::class, 'is not searchable');

it('can suppress automatic indexing for a bulk import', function () {
    Embeddings::fake([[[0.1, 0.2, 0.3]]]);

    config()->set('intelligence.search.auto_index', true);

    Role::withoutAiIndexing(function (): void {
        Role::factory()->count(3)->create();
    });

    $this->assertDatabaseCount('ai_embeddings', 0);
});
