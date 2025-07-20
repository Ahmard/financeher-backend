# 1. Base with PHP & extensions
FROM php:8.4.10-fpm AS base

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    zip unzip libzip-dev supervisor \
  && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 2. Final image
FROM base AS production

WORKDIR /var/www

# 2.1 Copy only composer files and install deps (no scripts)
COPY composer.json composer.lock ./
RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# 2.2 Copy rest of the app code (including artisan)
COPY . .

# 2.3 Now run any remaining scripts
RUN composer run-script post-autoload-dump \
  && php artisan package:discover --ansi

# 2.4 Supervisor config
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 2.5 Permissions
RUN mkdir -p storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
