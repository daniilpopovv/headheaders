FROM php:8.1.1-fpm AS php

WORKDIR /var/www/symfony_docker

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip libpq-dev \
    && docker-php-ext-install intl opcache pdo pdo_pgsql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./app/composer.json .
COPY ./app/composer.lock .

RUN composer update

RUN composer install

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - &&\
    apt-get install -y nodejs

COPY ./app/package.json .
COPY ./app/package-lock.json .

RUN npm install

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

COPY ./app .

RUN symfony run -d npm run watch

COPY ./run.sh /tmp
RUN chmod +x /tmp/run.sh
ENTRYPOINT ["/tmp/run.sh"]