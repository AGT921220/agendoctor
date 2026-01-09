#!/usr/bin/env sh
set -eu

cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

if ! php -r 'exit((bool) preg_match("/^APP_KEY=base64:/m", @file_get_contents(".env") ?: "") ? 0 : 1);'; then
  php artisan key:generate --force
fi

if [ ! -f storage/logs/laravel.log ]; then
  mkdir -p storage/logs
  touch storage/logs/laravel.log
fi

php artisan config:clear >/dev/null 2>&1 || true

exec "$@"

