FROM node:19-alpine AS assets
WORKDIR /app/
RUN apk update && apk upgrade

COPY ./app/package.json ./app/package-lock.json /app/

COPY ./app/vendor/symfony/ux-autocomplete/assets /app/vendor/symfony/ux-autocomplete/assets

RUN npm install

COPY ./app/webpack.config.js /app/
COPY ./app/assets /app/assets
RUN npm run build


FROM php:8.1-fpm AS base
WORKDIR /app/
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt -y update \
        && apt install -y g++ git libicu-dev zip libzip-dev libpq-dev \
        && docker-php-ext-install intl opcache pdo pdo_pgsql

FROM base AS build
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY ./app/composer.lock ./app/composer.json ./app/symfony.lock ./
RUN composer install -n --no-scripts

#nginx
COPY ./build/nginx/default.conf /etc/nginx/conf.d/default.conf
#php-fpm
COPY ./build/php/entrypoint.sh /etc/php/

COPY ./app .
COPY --from=assets /app/public/build /app/public/build