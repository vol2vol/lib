#!/bin/sh
set -e
cd /var/www/html/backend
cp .env.example .env
touch database/database.sqlite
composer install
php artisan key:generate
php artisan migrate:fresh --seed
exec docker-php-entrypoint php-fpm