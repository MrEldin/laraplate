# Laraplate

Laraplate is a Laravel API boilerplate, built on **Laravel 13**, PHP 8.4+ and PostgreSQL.

It is built with these packages:

* [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth) — JWT authentication
* [spatie/laravel-permission](https://github.com/spatie/laravel-permission) — roles and permissions
* [spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog) — activity logging
* [andersao/l5-repository](https://github.com/andersao/l5-repository) — repository pattern
* [dingo/api](https://github.com/api-ecosystem-for-laravel/dingo-api) — versioned API router and Fractal transformers
* [laravel/ai](https://github.com/laravel/ai) — the official Laravel AI SDK, wrapped by the AI layer in `src/AI`

## Getting started

```bash
git clone git@github.com:MrEldin/laraplate.git
cd laraplate
docker compose up
```

That is the whole setup. On the first start the `app` container creates `.env`
from `.env.example`, installs Composer dependencies, generates the application
key and the JWT secret, waits for PostgreSQL, runs the migrations and seeds the
baseline roles, permissions and admin user. Every step is idempotent, so later
`docker compose up` runs only apply new migrations.

The API is then served at **http://localhost:8000**.

| Service    | Image               | Host port |
| ---------- | ------------------- | --------- |
| `web`      | `nginx:1.29-alpine` | 8000      |
| `app`      | built from `docker/Dockerfile` | — |
| `postgres` | `postgres:18-alpine`| 55432     |
| `redis`    | `redis:8-alpine`    | 56379     |

PostgreSQL and Redis are published on deliberately unusual host ports so that a
local (or another project's) database never blocks `docker compose up`. Inside
the compose network they still answer on 5432 and 6379.

Override `APP_PORT`, `DB_FORWARD_PORT`, `REDIS_FORWARD_PORT`, `DB_DATABASE`,
`DB_USERNAME`, `DB_PASSWORD`, `PHP_VERSION`, `UID` or `GID` in the shell (or in
a local `.env`) to change the defaults.

### Working in the container

```bash
docker compose exec app bash
docker compose exec app php artisan migrate:status
docker compose exec app php artisan test
```

### Seeded credentials

The `UsersTableSeeder` creates a `super-admin` user:

```
email:    admin@mail.com
password: password
```

## Running without Docker

Requires PHP 8.4+ and a reachable PostgreSQL instance.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

## AI

Everything AI lives in [`src/AI`](src/AI). It sits on top of the Laravel AI SDK
and adds one idea: **an entity can describe itself to a model, safely.**

### Give an entity AI powers

Add the trait and the contract; nothing else is required.

```php
use Laraplate\AI\Concerns\HasAi;
use Laraplate\AI\Contracts\HasAiContext;

class Invoice extends Model implements HasAiContext
{
    use HasAi;
}
```

That entity can now reason about itself:

```php
$invoice->ai()->ask('Is anything about this invoice unusual?');   // prose
$invoice->ai()->summarize();                                     // structured
$invoice->ai()->classify(['paid', 'overdue', 'disputed']);        // structured
$invoice->ai()->stream('Explain this invoice line by line.');     // streamed
$invoice->ai()->queue('Draft a payment reminder.');               // queued
```

Teach it more about itself by overriding any of the trait's defaults:

```php
public function aiDescription(): string
{
    return 'An invoice issued to a customer. Amounts are in minor units.';
}

/** Relations the model may read, keyed by the name it sees. */
public function aiRelations(): array
{
    return ['line items' => 'lineItems', 'customer' => 'customer'];
}

/** Never let these reach a prompt. */
public function aiRedactedAttributes(): array
{
    return [...parent::aiRedactedAttributes(), 'internal_notes'];
}
```

### Use it from a service, through the facade

```php
use Laraplate\AI\Facades\Intelligence;

$verdict = Intelligence::for($user)
    ->withTools(new EntityLookupTool(Role::class))
    ->classify(['healthy', 'dormant', 'over_privileged', 'suspicious']);

$verdict['label'];       // constrained to the labels above by the schema
$verdict['confidence'];
```

`Intelligence::for($entity)` and `$entity->ai()` return the same object, so a
service never has to reach into the entity to build a prompt.
[`UserRiskAssessmentService`](src/Entities/User/Services/UserRiskAssessmentService.php)
is a worked example.

The facade also covers entity-free work:

```php
Intelligence::ask('Summarise our permission model.');
Intelligence::askEach($users, 'Does this account look abandoned?');
```

### What the layer guarantees

* **Redaction is structural.** Attributes are filtered when the context is
  built, not at the call site, so no agent can reach a password hash — including
  through relations and lookup tools.
* **Guardrails are inherited.** Every agent extending `EntityAgent` gets the
  same rules, including treating record contents as data rather than
  instructions to the model.
* **Tests never call a provider.** Feature tests run under
  `Http::preventStrayRequests()`; an un-faked agent fails the test instead of
  spending money.

### Writing your own agent

```php
class InvoiceDisputeAgent extends \Laraplate\AI\Agents\EntityAgent
{
    protected function role(): string
    {
        return 'You assess whether an invoice dispute is likely to succeed.';
    }
}

$invoice->ai()->agent(InvoiceDisputeAgent::class)->prompt('Assess this dispute.');
```

The SDK's own generators are available too: `php artisan make:agent`,
`make:tool`, `make:agent-middleware`, and `php artisan ai:chat` for a REPL.

### Configuration

`config/ai.php` defaults to Anthropic. Set `ANTHROPIC_API_KEY` to enable it;
`AI_PROVIDER` switches providers (OpenAI, Gemini, Bedrock, Ollama and others are
supported out of the box).

## Testing

```bash
php artisan test          # or: ./vendor/bin/pest
```

Tests are written in [Pest](https://pestphp.com). The suite runs against
in-memory SQLite for speed; CI additionally applies the migrations and seeders
to PostgreSQL, which is what the application ships on.

## Code generation

Laraplate ships generators that scaffold a full entity — model, repository,
contract, service provider, controller, transformer, requests and routes — into
`src/Entities`:

```bash
php artisan make:l-entity Post
```

Run `php artisan list make` to see the individual generators. Paths and
namespaces are configured in `config/nar.php`.

## API routes

Dingo registers the API routes on its own router, so use:

```bash
php artisan api:routes
```

`php artisan route:list` shows the regular Laravel routes.

## Contribution guidelines

* Write tests — see the PHPUnit documentation.
* Open a pull request for every new feature.
