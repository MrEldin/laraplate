<?php

use Illuminate\Support\Facades\Http;
use Laraplate\Serializers\CustomSerializer;
use PHPOpenSourceSaver\Fractal\Manager;
use PHPOpenSourceSaver\Fractal\Resource\ResourceInterface;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Feature tests boot the application, migrate a fresh database, seed the
| baseline roles and permissions and sign in a super-admin. Unit tests stay
| free of the framework so they remain fast.
|
*/

pest()->extend(TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| No Outbound Requests
|--------------------------------------------------------------------------
|
| The AI layer talks to providers over Laravel's HTTP client. A test that
| forgets to fake an agent would otherwise reach a real provider -- costing
| money and failing in CI for the wrong reason. Fail fast instead.
|
*/

pest()->beforeEach(fn () => Http::preventStrayRequests())->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

/**
 * Assert that the response body matches what Fractal would render for the
 * given resource, which is how the API serialises every entity.
 */
expect()->extend('toMatchTransformed', function (ResourceInterface $resource) {
    $manager = (new Manager)->setSerializer(new CustomSerializer);

    expect($this->value->getContent())->toEqual($manager->createData($resource)->toJson());

    return $this;
});

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * The request headers carrying the authenticated user's JWT.
 */
function authHeaders(): array
{
    return test()->getRequestHeaders();
}

/**
 * The currently authenticated user.
 */
function authenticatedUser(): \Laraplate\Entities\User\Models\User
{
    return test()->authenticatedUser;
}
