#!/bin/sh
set -eu

php artisan config:clear >/dev/null 2>&1 || true
php artisan cache:clear >/dev/null 2>&1 || true

php artisan storage:link || true
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\RoleSeeder --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
