#!/bin/bash
composer install
php app/console cache:clear
php app/console assets:install
php app/console assetic:dump
php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
phpunit -c app/
php app/console server:run 0.0.0.0:8000