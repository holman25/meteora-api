FROM php:8.3-fpm-alpine

# 1) Extensiones PHP necesarias (ajusta a tu app)
RUN docker-php-ext-install pdo pdo_mysql

# 2) Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 3) Nginx + supervisord
RUN apk add --no-cache nginx supervisor

# 4) Código de la app
WORKDIR /var/www/html
COPY . /var/www/html

# 5) Dependencias y caches
RUN composer install --no-dev --optimize-autoloader \
 && php artisan key:generate --force \
 && php artisan config:cache && php artisan route:cache

# 6) Nginx conf (ajusta el server_name y root si es necesario)
#    IMPORTANTE: que Nginx escuche $PORT
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN sed -i 's/listen 80;/listen ${PORT};/g' /etc/nginx/conf.d/default.conf

# 7) Supervisord: levanta php-fpm + nginx
COPY docker/supervisord.conf /etc/supervisord.conf

# 8) Static/public symlink
RUN php artisan storage:link || true

ENV PORT=8080
EXPOSE 8080

CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]
