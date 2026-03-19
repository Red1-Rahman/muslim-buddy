FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    sqlite \
    sqlite-dev \
    nginx \
    supervisor

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_sqlite pcntl bcmath gd xml

# Install libsql PHP extension for Turso
RUN LIBSQL_VERSION=$(curl -fsSL https://api.github.com/repos/tursodatabase/turso-client-php/releases/latest \
        | grep '"tag_name"' | sed 's/.*"tag_name": "\(.*\)".*/\1/') \
    && echo "LibSQL version: $LIBSQL_VERSION" \
    && curl -fsSL "https://github.com/tursodatabase/turso-client-php/releases/download/${LIBSQL_VERSION}/turso-client-php_Linux_x86_64.tar.gz" \
        -o /tmp/libsql.tar.gz \
    && mkdir -p /tmp/libsql \
    && tar -xzf /tmp/libsql.tar.gz -C /tmp/libsql \
    && echo "Files extracted:" \
    && ls -la /tmp/libsql/ \
    && EXT_DIR=$(php -r "echo ini_get('extension_dir');") \
    && echo "PHP extension dir: $EXT_DIR" \
    && find /tmp/libsql -name "*.so" | while read f; do \
           echo "Copying $f to $EXT_DIR/"; \
           cp "$f" "$EXT_DIR/"; \
       done \
    && ls "$EXT_DIR/" | grep libsql \
    && SO_FILE=$(ls "$EXT_DIR/" | grep libsql | head -1) \
    && echo "extension=$SO_FILE" > /usr/local/etc/php/conf.d/libsql.ini \
    && echo "INI contents:" \
    && cat /usr/local/etc/php/conf.d/libsql.ini \
    && rm -rf /tmp/libsql /tmp/libsql.tar.gz \
    && php -r "echo 'LibSQL class exists: '; var_dump(class_exists('LibSQL'));" \
    && php -m | grep -i libsql

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
