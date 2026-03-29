#!/bin/sh
set -eu

echo "[startup] clearing Laravel caches"
php artisan config:clear >/dev/null 2>&1 || true
php artisan cache:clear >/dev/null 2>&1 || true

echo "[startup] creating storage symlink"
php artisan storage:link || true

echo "[startup] running database migrations"
php artisan migrate --force

echo "[startup] seeding base roles"
php artisan db:seed --class=Database\\Seeders\\RoleSeeder --force

echo "[startup] starting Laravel on port ${PORT:-8080}"
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
