# Stage 1: Build Composer dependencies
FROM composer:2.8 AS composer-stage
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --no-plugins

COPY . .
RUN composer dump-autoload --optimize --no-dev

# Stage 2: Build Frontend assets with Vite
FROM node:22-alpine AS frontend-stage
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# Stage 3: Production Runtime (PHP 8.4 + Nginx + Supervisor on Debian Bookworm)
FROM php:8.4-fpm-bookworm AS production-runtime

# Install system dependencies, Nginx, Supervisor, CA certificates, and runtime libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    bash \
    curl \
    ca-certificates \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        xml \
        ctype \
        fileinfo \
        curl \
        zip \
        bcmath \
        opcache \
        gd \
        intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application source
COPY . /var/www/html

# Copy vendor from composer-stage
COPY --from=composer-stage /app/vendor /var/www/html/vendor

# Copy compiled assets from frontend-stage
COPY --from=frontend-stage /app/public/build /var/www/html/public/build

# Copy configuration files
COPY docker/default.conf.template /etc/nginx/templates/default.conf.template
COPY docker/php.ini /usr/local/etc/php/conf.d/99-production.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-docker-custom.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh

# Make entrypoint executable and prepare directories
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p /var/www/html/storage/framework/cache/data \
                /var/www/html/storage/framework/sessions \
                /var/www/html/storage/framework/views \
                /var/www/html/storage/logs \
                /var/www/html/storage/app/public \
                /var/www/html/storage/app/private \
                /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose default port (Render will override via $PORT)
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
