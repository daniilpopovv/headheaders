FROM php:8.1-fpm AS base
WORKDIR /app/
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt -y update \
        && apt install -y g++ git nginx libicu-dev zip libzip-dev libpq-dev \
        && docker-php-ext-install intl opcache pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM base AS composer
COPY composer.lock composer.json symfony.lock /app/
RUN composer install -n \
    --no-progress \
    --no-plugins \
    --no-autoloader \
    --no-scripts \
    --no-suggest
#добавить для прода --no-dev

FROM node:18.3-alpine AS assets
WORKDIR /app/
RUN apk update && apk upgrade

COPY package.json package-lock.json /app/
COPY --from=composer /app/vendor/symfony/ux-autocomplete/assets /app/vendor/symfony/ux-autocomplete/assets

RUN npm install --no-progress

COPY webpack.config.js /app/
COPY assets /app/assets/
RUN npm run build

FROM base
COPY --from=composer /app/vendor /app/vendor
COPY --from=assets /app/public/build /app/public/build

#entrypoint
COPY ./infrastructure/docker/scripts/php-nginx/entrypoint.sh /etc/service/
#nginx config
COPY ./infrastructure/docker/config/nginx/default.conf /etc/nginx/conf.d/default.conf

COPY . .

RUN composer dump-autoload --optimize

RUN chmod +x /etc/service/entrypoint.sh
CMD ["/etc/service/entrypoint.sh"]