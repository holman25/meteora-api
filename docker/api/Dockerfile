FROM php:8.3-fpm-alpine

RUN apk add --no-cache postgresql-dev nginx supervisor \
 && docker-php-ext-install pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN sed -i 's/listen 80;/listen ${PORT};/g' /etc/nginx/conf.d/default.conf

COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENV PORT=8080
EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]
