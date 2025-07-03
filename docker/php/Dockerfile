FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash git icu-dev oniguruma-dev libzip-dev zip unzip \
    mysql-client curl

RUN docker-php-ext-install pdo_mysql intl opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

CMD ["php-fpm"]
