#!/usr/bin/env bash
#
# Prepares the application on every container start so that a freshly cloned
# repository needs nothing beyond "docker compose up". Every step is idempotent.
#
set -euo pipefail

cd /var/www/html

log() { printf '\033[0;34m[entrypoint]\033[0m %s\n' "$1"; }

# --- .env --------------------------------------------------------------------
if [ ! -f .env ]; then
    log 'No .env found, creating one from .env.example'
    cp .env.example .env
fi

# --- dependencies ------------------------------------------------------------
# The project directory is bind mounted, which hides the vendor tree baked into
# the image, so install into the mount on first boot.
if [ ! -f vendor/autoload.php ]; then
    log 'Installing Composer dependencies'
    composer install --no-interaction --prefer-dist
fi

# --- application keys --------------------------------------------------------
if ! grep -qE '^APP_KEY=base64:.+' .env; then
    log 'Generating application key'
    php artisan key:generate --force --ansi
fi

if ! grep -qE '^JWT_SECRET=.+' .env; then
    log 'Generating JWT secret'
    php artisan jwt:secret --force --ansi
fi

# --- writable paths ----------------------------------------------------------
mkdir -p storage/framework/{cache/data,sessions,testing,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rw storage bootstrap/cache

# --- database ----------------------------------------------------------------
if [ "${DB_CONNECTION:-pgsql}" = "pgsql" ]; then
    log "Waiting for PostgreSQL at ${DB_HOST:-postgres}:${DB_PORT:-5432}"
    until pg_isready \
        --host="${DB_HOST:-postgres}" \
        --port="${DB_PORT:-5432}" \
        --username="${DB_USERNAME:-laraplate}" \
        --quiet; do
        sleep 1
    done
    log 'PostgreSQL is ready'
fi

# Seed only when the schema is created for the first time, so restarts never
# duplicate the baseline roles, permissions and admin user. migrate:status exits
# non-zero while the migrations table is still missing.
needs_seed=0
if ! php artisan migrate:status >/dev/null 2>&1; then
    needs_seed=1
fi

log 'Running database migrations'
php artisan migrate --force --ansi

if [ "$needs_seed" = "1" ]; then
    log 'Seeding the baseline roles, permissions and admin user'
    php artisan db:seed --force --ansi
fi

# --- caches ------------------------------------------------------------------
# Stale caches from a previous build would otherwise survive in the bind mount.
php artisan config:clear --ansi
php artisan route:clear --ansi

log 'Application ready'

exec "$@"
