#!/bin/sh

sed -i "s/Listen 80/Listen ${PORT:-10000}/" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT:-10000}/" /etc/apache2/sites-available/000-default.conf

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan admin:reset-password
php artisan view:cache
exec apache2-foreground