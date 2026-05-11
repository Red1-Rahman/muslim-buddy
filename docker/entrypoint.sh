#!/bin/sh
set -e

cd /var/www

# Debug: print DB env vars to confirm they reach the container
echo "=== ENV DEBUG ==="
echo "DB_CONNECTION=$DB_CONNECTION"
echo "TURSO_DB_URL=$TURSO_DB_URL"
echo "TURSO_DB_TOKEN length=$(echo -n $TURSO_DB_TOKEN | wc -c)"
echo "================="

# Write env vars to .env file so Laravel can read them reliably (replace existing values)
sed -i "s|^TURSO_DB_URL=.*|TURSO_DB_URL=${TURSO_DB_URL}|" /var/www/.env
sed -i "s|^TURSO_DB_TOKEN=.*|TURSO_DB_TOKEN=${TURSO_DB_TOKEN}|" /var/www/.env
sed -i "s|^DB_CONNECTION=.*|DB_CONNECTION=${DB_CONNECTION}|" /var/www/.env

# Sync Quran API env vars into .env (only when set in environment)
[ -n "$QURAN_API_BASE_URL" ] && sed -i "s|^QURAN_API_BASE_URL=.*|QURAN_API_BASE_URL=${QURAN_API_BASE_URL}|" /var/www/.env
[ -n "$QURAN_INFO_API_BASE_URL" ] && sed -i "s|^QURAN_INFO_API_BASE_URL=.*|QURAN_INFO_API_BASE_URL=${QURAN_INFO_API_BASE_URL}|" /var/www/.env

# Debug: base URLs only, no secrets
echo "=== QURAN API ENV ==="
echo "QURAN_API_BASE_URL (env)=$(printenv QURAN_API_BASE_URL || echo '(not set)')"
grep '^QURAN_API_BASE_URL=' /var/www/.env || echo "QURAN_API_BASE_URL not in .env"
grep '^QURAN_INFO_API_BASE_URL=' /var/www/.env || echo "QURAN_INFO_API_BASE_URL not in .env"
echo "QURAN_API_AUTH_TOKEN length=$(printf '%s' "$QURAN_API_AUTH_TOKEN" | wc -c)"
echo "===================="

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
