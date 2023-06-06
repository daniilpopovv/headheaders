#!/bin/bash -E

php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
php bin/console assets:install
service nginx start
exec php-fpm -F
