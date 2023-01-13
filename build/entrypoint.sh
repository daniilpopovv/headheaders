#!/bin/bash -E

php bin/console doctrine:migrations:migrate -n
service nginx start
exec php-fpm -F