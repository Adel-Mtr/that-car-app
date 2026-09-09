#!/bin/sh
set -eu

mkdir -p \
    storage/app/private \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

if [ -z "${APP_KEY:-}" ]; then
    key_file="${APP_KEY_FILE:-/data/app-key}"

    if [ -s "$key_file" ]; then
        APP_KEY="$(cat "$key_file")"
    else
        APP_KEY="$(php artisan key:generate --show --no-ansi)"
        umask 077
        printf '%s\n' "$APP_KEY" > "$key_file"
    fi

    export APP_KEY
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
        database_path="${DB_DATABASE:-/data/database.sqlite}"
        mkdir -p "$(dirname "$database_path")"
        touch "$database_path"
    fi

    php artisan migrate --force

    if [ "${SEED_DEMO:-false}" = "true" ]; then
        user_count="$(php -r '
            $path = getenv("DB_DATABASE");
            $db = new PDO("sqlite:".$path);
            try { echo (int) $db->query("select count(*) from users")->fetchColumn(); }
            catch (Throwable $e) { echo 0; }
        ')"

        if [ "$user_count" = "0" ]; then
            php artisan db:seed --force
        fi
    fi
fi

exec "$@"
