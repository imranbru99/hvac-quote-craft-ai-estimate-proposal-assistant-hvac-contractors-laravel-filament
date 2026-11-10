#!/usr/bin/env sh
set -e
cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
  php artisan key:generate --force --no-interaction || true
  # Export generated key for current process
  APP_KEY=$(grep -E '^APP_KEY=' .env | cut -d= -f2-)
  export APP_KEY
fi

php artisan config:clear || true
php artisan migrate --force --no-interaction || true
php artisan db:seed --force --no-interaction || true
php artisan storage:link || true

exec "$@"