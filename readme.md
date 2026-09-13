# Laraplate

Laraplate is a Laravel API boilerplate, built on **Laravel 13**, PHP 8.4+ and PostgreSQL.

It is built with these packages:

* [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth) — JWT authentication
* [spatie/laravel-permission](https://github.com/spatie/laravel-permission) — roles and permissions
* [spatie/laravel-activitylog](https://github.com/spatie/laravel-activitylog) — activity logging
* [andersao/l5-repository](https://github.com/andersao/l5-repository) — repository pattern
* [dingo/api](https://github.com/api-ecosystem-for-laravel/dingo-api) — versioned API router and Fractal transformers

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
| `postgres` | `postgres:18-alpine`| 5432      |
| `redis`    | `redis:8-alpine`    | 6379      |

Override `APP_PORT`, `DB_PORT`, `REDIS_PORT`, `DB_DATABASE`, `DB_USERNAME`,
`DB_PASSWORD`, `PHP_VERSION`, `UID` or `GID` in the shell (or in a local `.env`)
to change the defaults.

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

## Testing

```bash
php artisan test
```

The suite runs against in-memory SQLite for speed. CI additionally applies the
migrations and seeders to PostgreSQL, which is what the application ships on.

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
