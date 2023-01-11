#!/bin/bash -e

php bin/console doctrine:migrations:migrate -n
exec php-fpm -F