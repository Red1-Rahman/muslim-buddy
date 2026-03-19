#!/bin/sh
set -e

cd /var/www

# Cache config, routes, views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Seed if surahs table is empty (production first-run only)
php artisan db:seed --class=ProductionSeeder --force

# Start supervisor (nginx + php-fpm)
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
