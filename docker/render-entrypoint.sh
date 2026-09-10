#!/bin/sh
set -eu
export APP_URL="${RENDER_EXTERNAL_URL:-${APP_URL:-http://localhost:10000}}"
export PORT="${PORT:-10000}"
exec sh docker/entrypoint.sh sh -c 'chown -R www-data:www-data /data storage bootstrap/cache; exec apache2-foreground'
