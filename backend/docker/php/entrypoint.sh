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

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
touch storage/logs/laravel.log 2>/dev/null || true
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

php artisan config:clear >/dev/null 2>&1 || true

# Esperar a MySQL y ejecutar migraciones
for i in 1 2 3 4 5 6 7 8 9 10; do
  if php artisan migrate --force 2>/dev/null; then
    break
  fi
  [ "$i" = 10 ] && echo "⚠️ Migrate falló; ejecuta: make migrate" || sleep 2
done

exec "$@"

