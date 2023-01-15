FROM node:19-alpine AS assets
WORKDIR /app/
RUN apk update && apk upgrade

COPY ./app/package.json ./app/package-lock.json /app/
COPY ./build/ux-autocomplete/assets /app/vendor/symfony/ux-autocomplete/assets

RUN npm install

COPY ./app/webpack.config.js /app/
COPY ./app/assets /app/assets
RUN npm run build


FROM php:8.1-fpm AS base
WORKDIR /app/
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt -y update \
        && apt install -y g++ git nginx libicu-dev zip libzip-dev libpq-dev \
        && docker-php-ext-install intl opcache pdo pdo_pgsql

FROM base
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY ./app/composer.lock ./app/composer.json ./app/symfony.lock ./
RUN composer install -n --no-scripts

COPY ./app .
COPY --from=assets /app/public/build /app/public/build

RUN chown -R www-data:www-data /app/var/*

#nginx config
COPY ./build/nginx/default.conf /etc/nginx/conf.d/default.conf

#entrypoint
COPY ./build/entrypoint.sh /etc/service/
RUN chmod +x /etc/service/entrypoint.sh
CMD ["/etc/service/entrypoint.sh"]