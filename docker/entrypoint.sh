#!/usr/bin/env sh
set -e

php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan migrate --force || true

exec "$@"
