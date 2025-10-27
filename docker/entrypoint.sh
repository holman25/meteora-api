#!/usr/bin/env sh
set -e

envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf
php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan migrate --force || true

exec "$@"
