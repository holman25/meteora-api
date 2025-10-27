#!/usr/bin/env sh
set -e

export PORT="${PORT:-8080}"

envsubst '${PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf
mkdir -p /run/nginx

php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
# php artisan migrate --force || true
php-fpm -t || true
nginx -t || { echo "Nginx config invalid"; cat /etc/nginx/conf.d/default.conf; exit 1; }

exec "$@"
