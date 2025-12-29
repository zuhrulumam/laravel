# Stage 1: Build PHP dependencies
FROM composer:2.7 as vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# Stage 2: Final Image
FROM dunglas/frankenphp:1.2-php8.3-alpine

# Install Laravel system requirements
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo_mysql \
    intl \
    zip \
    opcache \
    pcntl

# Set working directory
WORKDIR /app

# Copy application and vendor from build stage
COPY --from=vendor /app/vendor /app/vendor
COPY . .

# Set permissions for Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Production PHP settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Set FrankenPHP environment variables
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV APP_ENV=production
ENV APP_RUNTIME=Laravel\Octane\FrankenPHP\Runtime

# Expose the port Easypanel expects (usually 80 or 8080)
EXPOSE 80

# Run the app
ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=80"]
