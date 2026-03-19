#!/bin/sh
set -e

cd /var/www

# Debug: print DB env vars to confirm they reach the container
echo "=== ENV DEBUG ==="
echo "DB_CONNECTION=$DB_CONNECTION"
echo "TURSO_DB_URL=$TURSO_DB_URL"
echo "TURSO_DB_TOKEN length=$(echo -n $TURSO_DB_TOKEN | wc -c)"
echo "================="

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
