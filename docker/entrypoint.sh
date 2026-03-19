#!/bin/sh
set -e

cd /var/www

# Debug: print DB env vars to confirm they reach the container
echo "=== ENV DEBUG ==="
echo "DB_CONNECTION=$DB_CONNECTION"
echo "TURSO_DB_URL=$TURSO_DB_URL"
echo "TURSO_DB_TOKEN length=$(echo -n $TURSO_DB_TOKEN | wc -c)"
echo "================="

# Write env vars to .env file so Laravel can read them reliably
echo "TURSO_DB_URL=${TURSO_DB_URL}" >> /var/www/.env
echo "TURSO_DB_TOKEN=${TURSO_DB_TOKEN}" >> /var/www/.env
echo "DB_CONNECTION=${DB_CONNECTION}" >> /var/www/.env

# Clear any cached config so fresh values are used
php artisan config:clear

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
