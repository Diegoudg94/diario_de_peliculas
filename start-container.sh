#!/bin/sh
set -eu

cd /app

if [ -n "${RAILWAY_ENVIRONMENT:-}${RAILWAY_PROJECT_ID:-}" ]; then
    export APP_ENV=production
    export APP_DEBUG=false
    export DB_CONNECTION=sqlite
    export DB_DATABASE=/data/database.sqlite
    export CACHE_STORE=file
    export CACHE_DRIVER=file
    export SESSION_DRIVER=file
    export QUEUE_CONNECTION=sync
    export PORT="${PORT:-8080}"
    unset DB_URL

    if [ -z "${APP_KEY:-}" ]; then
        echo 'Falta APP_KEY en las variables del servicio.' >&2
        exit 1
    fi
    if [ "${RAILWAY_VOLUME_MOUNT_PATH:-}" != /data ]; then
        echo 'Adjunta un volumen Railway al servicio con Mount Path /data y aplica Deploy.' >&2
        exit 1
    fi
    if [ ! -d /data ] || [ ! -w /data ]; then
        echo 'El directorio /data no existe o no permite escritura.' >&2
        exit 1
    fi
else
    export DB_CONNECTION="${DB_CONNECTION:-sqlite}"
    export DB_DATABASE="${DB_DATABASE:-database/database.sqlite}"
    export PORT="${PORT:-8080}"
fi

if ! php -r 'exit(extension_loaded("pdo_sqlite") ? 0 : 1);'; then
    echo 'Falta la extensión PHP pdo_sqlite.' >&2
    exit 1
fi

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

if [ "$DB_CONNECTION" = sqlite ] && [ "$DB_DATABASE" != ':memory:' ]; then
    mkdir -p "$(dirname "$DB_DATABASE")"
    if [ ! -e "$DB_DATABASE" ]; then
        touch "$DB_DATABASE"
    fi
    if [ ! -f "$DB_DATABASE" ] || [ ! -w "$DB_DATABASE" ]; then
        echo "La base SQLite $DB_DATABASE no es un archivo con permisos de escritura." >&2
        exit 1
    fi
fi

php artisan config:clear
php artisan migrate --force

exec docker-php-entrypoint --config /Caddyfile --adapter caddyfile
