#!/bin/sh
set -eu

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

if [ ! -f .env ] && [ -f .env.example ] && [ "${APP_ENV:-local}" != "production" ]; then
    cp .env.example .env
fi

if [ -f .env ] && [ "${APP_ENV:-local}" != "production" ] && ! grep -Eq '^APP_KEY=.+$' .env; then
    php artisan key:generate --no-interaction
fi

exec "$@"
