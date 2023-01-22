#!/bin/bash -E

php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
service nginx start
exec php-fpm -F
