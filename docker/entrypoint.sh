#!/bin/sh
set -e

cd /var/www

# Run migrations first (needs DB connection)
php artisan migrate --force

# Seed if empty
php artisan db:seed --class=ProductionSeeder --force

# Cache config/routes/views AFTER migrations succeed
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start supervisor (nginx + php-fpm)
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf
