FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    libsqlite3-dev \
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_sqlite pcntl bcmath gd xml

# Install libsql PHP extension for Turso (requires GNU libc - Debian compatible)
RUN curl -fsSL \
    "https://github.com/tursodatabase/turso-client-php/releases/download/turso-php-extension-v1.6.2/libsql_php-turso-php-extension-v1.6.2-php-8.2-nts-x86_64-unknown-linux-gnu.tar.gz" \
    -o /tmp/libsql.tar.gz \
    && mkdir -p /tmp/libsql \
    && tar -xzf /tmp/libsql.tar.gz -C /tmp/libsql \
    && EXT_DIR=$(php -r "echo ini_get('extension_dir');") \
    && find /tmp/libsql -name "*.so" -exec cp {} "$EXT_DIR/" \; \
    && SO_FILE=$(ls "$EXT_DIR/" | grep -i libsql | head -1) \
    && echo "extension=$SO_FILE" > /usr/local/etc/php/conf.d/libsql.ini \
    && rm -rf /tmp/libsql /tmp/libsql.tar.gz \
    && php -m | grep -i libsql && echo "libsql extension loaded successfully"

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Create required directories and set permissions before composer
RUN mkdir -p /var/www/bootstrap/cache \
             /var/www/storage/logs \
             /var/www/storage/framework/cache \
             /var/www/storage/framework/sessions \
             /var/www/storage/framework/views \
    && chmod -R 775 /var/www/bootstrap/cache /var/www/storage

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Patch the buggy Turso driver so it doesn't clear our libsql:// network URL
RUN sed -i "s/\$config\['url'\] = \$this->checkPathOrFilename/\\/\/ \$config\['url'\] = \$this->checkPathOrFilename/" /var/www/vendor/tursodatabase/turso-driver-laravel/src/Database/LibSQLDatabase.php

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
