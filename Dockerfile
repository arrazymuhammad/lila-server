# TASK-361: Multi-stage Docker build < 150MB target
FROM php:8.3-fpm-alpine AS base

# System deps for PHP extensions
RUN apk add --no-cache \
    libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev \
    icu-dev oniguruma-dev linux-headers \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && pecl install apcu && docker-php-ext-enable apcu opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Production stage
FROM base AS production

WORKDIR /var/www/html

# Copy composer files first (layer cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
    && composer dump-autoload --optimize --no-dev

# Copy application code
COPY . .

# Optimize Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
