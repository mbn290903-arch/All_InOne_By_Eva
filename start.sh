#!/bin/sh
sed -i "s/Listen 80/Listen ${PORT:-10000}/" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT:-10000}/" /etc/apache2/sites-available/000-default.conf
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true
php artisan migrate --force
echo "CREATE_ADMIN=[$CREATE_ADMIN] RUN_SEED=[$RUN_SEED]"
if [ "$CREATE_ADMIN" = "true" ]; then php artisan app:create-admin 2>&1; echo "create-admin code retour: $?"; fi
if [ "$RUN_SEED" = "true" ]; then php artisan db:seed --force; fi
exec apache2-foreground