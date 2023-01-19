FROM php:8.1-fpm AS base
WORKDIR /app/
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt -y update \
        && apt install -y g++ git nginx libicu-dev zip libzip-dev libpq-dev \
        && docker-php-ext-install intl opcache pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM base AS composer
COPY ./app/composer.lock ./app/composer.json ./app/symfony.lock ./
RUN composer install -n \
    --no-progress \
    --no-plugins \
    --no-autoloader \
    --no-scripts \
    --no-suggest
#добавить для прода --no-dev

FROM node:lts-alpine AS assets
WORKDIR /app/
RUN apk update && apk upgrade

COPY ./app/package.json ./app/package-lock.json /app/
COPY --from=composer /app/vendor /app/vendor

RUN npm install

COPY ./app/webpack.config.js /app/
COPY ./app/assets /app/assets
RUN npm run build

FROM base
COPY --from=composer /app/vendor /app/vendor
COPY --from=assets /app/public/build /app/public/build
COPY ./app /app

#nginx config
COPY ./build/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN composer dump-autoload --optimize

#entrypoint
COPY ./build/entrypoint.sh /etc/service/
RUN chmod +x /etc/service/entrypoint.sh
CMD ["/etc/service/entrypoint.sh"]
