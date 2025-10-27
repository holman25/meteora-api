#!/usr/bin/env sh
set -e

export PORT="${PORT:-8080}"
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

envsubst '${PORT}' < /etc/nginx/http.d/default.conf.template > /etc/nginx/http.d/default.conf
mkdir -p /run/nginx

php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan migrate --force || true

php-fpm -t || true
nginx -t || { echo "Nginx config invalid"; cat /etc/nginx/http.d/default.conf; exit 1; }

exec "$@"
