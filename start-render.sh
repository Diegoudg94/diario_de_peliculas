#!/bin/sh
set -eu

cd /var/www/html

export APP_ENV=production
export APP_DEBUG=false
export DB_CONNECTION=pgsql
export DB_SSLMODE=require
export CACHE_STORE=file
export SESSION_DRIVER=file
export QUEUE_CONNECTION=sync
export PORT="${PORT:-10000}"

if [ -z "${APP_KEY:-}" ] || [ -z "${APP_URL:-}" ]; then
    echo 'Faltan APP_KEY o APP_URL en las variables de Render.' >&2
    exit 1
fi

case "$APP_URL" in
    https://*) ;;
    *) echo 'APP_URL debe ser la URL pública HTTPS de Render.' >&2; exit 1 ;;
esac

case "$PORT" in
    ''|*[!0-9]*) echo 'PORT debe ser numérico.' >&2; exit 1 ;;
esac
if [ "$PORT" -lt 1 ] || [ "$PORT" -gt 65535 ]; then
    echo 'PORT debe estar entre 1 y 65535.' >&2
    exit 1
fi

if [ -n "${DB_URL:-}" ]; then
    if ! php -r '
        $url = parse_url(getenv("DB_URL"));
        if ($url === false || !in_array($url["scheme"] ?? "", ["postgres", "postgresql"], true) || empty($url["host"]) || empty($url["path"])) exit(1);
        parse_str($url["query"] ?? "", $query);
        if (!in_array($query["sslmode"] ?? "require", ["require", "verify-ca", "verify-full"], true)) exit(1);
    '; then
        echo 'DB_URL debe ser una URL PostgreSQL válida con SSL obligatorio.' >&2
        exit 1
    fi
else
    if [ -z "${DB_HOST:-}" ] || [ -z "${DB_DATABASE:-}" ] || [ -z "${DB_USERNAME:-}" ] || [ -z "${DB_PASSWORD:-}" ]; then
        echo 'Configura DB_URL o DB_HOST, DB_DATABASE, DB_USERNAME y DB_PASSWORD.' >&2
        exit 1
    fi
fi

if ! php -r 'exit(extension_loaded("pdo_pgsql") ? 0 : 1);'; then
    echo 'Falta la extensión pdo_pgsql.' >&2
    exit 1
fi

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

printf 'Listen 0.0.0.0:%s\n' "$PORT" > /etc/apache2/ports.conf
sed "s/__PORT__/$PORT/g" /etc/apache2/sites-available/cinediario.conf.template > /etc/apache2/sites-available/000-default.conf

php artisan config:clear
php artisan migrate --force

exec apache2-foreground
