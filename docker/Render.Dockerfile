# syntax=docker/dockerfile:1.7
FROM php:8.4-apache-bookworm AS php-base
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip libcurl4-openssl-dev libonig-dev libsqlite3-dev libxml2-dev \
    && docker-php-ext-install -j"$(nproc)" curl dom mbstring pdo_sqlite xml xmlwriter \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app

FROM php-base AS php-dependencies
COPY composer.json composer.lock ./
# Faker is required by the fictional demo seeder; this image is for the demo only.
RUN composer install --prefer-dist --no-interaction --no-progress --no-scripts --optimize-autoloader

FROM node:22-bookworm-slim AS frontend-assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY . .
COPY --from=php-dependencies /app/vendor ./vendor
RUN mkdir -p public storage/framework/views && npm run build

FROM php-base AS runtime
COPY . .
COPY --from=php-dependencies /app/vendor ./vendor
COPY --from=frontend-assets /app/public/build ./public/build
COPY docker/apache-render.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite \
    && truncate -s 0 /etc/apache2/ports.conf \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && mkdir -p /data storage/app/private storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data /data storage bootstrap/cache
ENV PORT=10000
EXPOSE 10000
ENTRYPOINT ["sh", "docker/render-entrypoint.sh"]
