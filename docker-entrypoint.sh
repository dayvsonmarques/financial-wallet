#!/bin/bash
set -e

APP_DIR="/var/www/html"

if [ "$(id -u)" = "0" ]; then
    # Prepare as root to avoid permission issues on bind mounts
    umask 0002
    cd "$APP_DIR"
    mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true

    if [ ! -f .env ] && [ -f .env.example ]; then
        cp .env.example .env
    fi

    if [ ! -f vendor/autoload.php ]; then
        composer install --no-interaction --prefer-dist
    fi

    chown -R www-data:www-data vendor storage bootstrap/cache .env 2>/dev/null || true
    chmod 664 .env 2>/dev/null || true

    # Generate app key as www-data (safe if already set)
    gosu www-data bash -lc "cd '$APP_DIR' && php artisan key:generate --force || true"

    # Finally run the passed command as www-data
    exec gosu www-data bash -lc "cd '$APP_DIR' && exec $*"
else
    cd "$APP_DIR"
    mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true

    if [ ! -f .env ] && [ -f .env.example ]; then
        cp .env.example .env
    fi

    if [ ! -f vendor/autoload.php ]; then
        composer install --no-interaction --prefer-dist
    fi

    if ! grep -q '^APP_KEY=' .env 2>/dev/null || [ -z "$(grep -oE '^APP_KEY=.+' .env 2>/dev/null | cut -d'=' -f2)" ]; then
        php artisan key:generate --force || true
    fi

    exec "$@"
fi
