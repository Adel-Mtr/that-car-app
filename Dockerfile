# syntax=docker/dockerfile:1.7

FROM php:8.4-cli-bookworm AS php-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        unzip \
        libcurl4-openssl-dev \
        libonig-dev \
        libpq-dev \
        libsqlite3-dev \
        libxml2-dev \
    && docker-php-ext-install -j"$(nproc)" \
        curl \
        dom \
        mbstring \
        pcntl \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        xml \
        xmlwriter \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app

FROM php-base AS php-dependencies
COPY composer.json composer.lock ./
RUN composer install \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader

FROM node:22-bookworm-slim AS frontend-assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY . .
COPY --from=php-dependencies /app/vendor ./vendor
RUN mkdir -p public storage/framework/views
RUN npm run build

FROM php-base AS runtime

COPY . .
COPY --from=php-dependencies /app/vendor ./vendor
COPY --from=frontend-assets /app/public/build ./public/build

RUN mkdir -p \
        /data \
        storage/app/private \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data /app /data \
    && chmod +x docker/entrypoint.sh

USER www-data

EXPOSE 8000
ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
