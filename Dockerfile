FROM php:8.1-fpm

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt update \
    && apt install -y g++ git libicu-dev zip libzip-dev libpq-dev \
    && docker-php-ext-install intl opcache pdo pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./app/composer.json ./app/composer.lock /app/

RUN composer install --no-scripts

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - &&\
    apt-get install -y nodejs

COPY ./app/package.json ./app/package-lock.json /app/

RUN npm install

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

COPY ./app /app/

RUN symfony run -d npm run build

COPY ./build/php/entrypoint.sh /app/
RUN chmod +x /app/entrypoint.sh
CMD ["/app/entrypoint.sh"]