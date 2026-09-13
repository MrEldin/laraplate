# syntax=docker/dockerfile:1

ARG PHP_VERSION=8.4

FROM php:${PHP_VERSION}-fpm-bookworm AS base

# mlocati's installer resolves the right system packages for each extension and
# keeps the image free of the build-only dependencies afterwards.
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
        bcmath \
        exif \
        gd \
        intl \
        opcache \
        pcntl \
        pdo_pgsql \
        pdo_sqlite \
        pgsql \
        redis \
        zip \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
        git \
        unzip \
        postgresql-client \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html

# php-fpm runs as www-data; the bind-mounted project must stay writable for it.
ARG UID=1000
ARG GID=1000
RUN groupmod -o -g "${GID}" www-data \
    && usermod -o -u "${UID}" -g "${GID}" www-data

# Warm the dependency cache into the image so a fresh clone does not have to
# download the whole vendor tree while the container is already starting.
COPY composer.json composer.lock ./
RUN composer install \
        --no-interaction \
        --no-autoloader \
        --no-scripts \
        --prefer-dist

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
