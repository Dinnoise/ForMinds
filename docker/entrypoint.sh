#!/bin/sh
set -e

# .env нужен artisan'у; реальные значения приходят из environment в docker-compose
[ -f .env ] || cp .env.example .env

if [ -z "$APP_KEY" ] && ! grep -q '^APP_KEY=base64' .env; then
    php artisan key:generate --force
fi

echo "Waiting for MySQL at $DB_HOST:$DB_PORT..."
until php -r 'try { new PDO("mysql:host=".getenv("DB_HOST").";port=".getenv("DB_PORT"), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); } catch (Exception $e) { exit(1); }' >/dev/null 2>&1; do
    sleep 1
done

php artisan package:discover --ansi
php artisan migrate --force
php artisan storage:link || true
php artisan basset:cache || true

if [ "$RUN_SEEDERS" = "true" ]; then
    php artisan db:seed --force || true
fi

exec "$@"
