#!/bin/sh
set -e
cd /var/www/html/backend
cp .env.example .env
cp -r database/seeders/books storage/app/private/books
cp -r database/seeders/covers storage/app/private/covers
touch database/database.sqlite
composer update
composer install
php artisan key:generate
php artisan migrate:fresh --seed
exec docker-php-entrypoint php-fpm