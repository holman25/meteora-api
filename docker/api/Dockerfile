FROM php:8.3-fpm

# Extensiones necesarias para Laravel
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip

# Composer dentro de la imagen
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Usuario www-data con uid/gid 1000 (compat con Windows montando volumen)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

CMD ["php-fpm"]
