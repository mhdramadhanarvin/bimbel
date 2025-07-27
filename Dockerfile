# Stage 1: PHP & Composer (Laravel Dependencies)
FROM php:8.3-fpm-alpine AS php_builder

# Install PHP extensions and system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    oniguruma-dev \
    zip \
    unzip \
    icu-dev \
    bash \
    openssl \
    postgresql-dev \
    shadow \
    && docker-php-ext-install pdo pdo_mysql mbstring zip intl opcache gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files (only PHP-related first for caching)
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of the application files
COPY . .

# Stage 2: Node build (frontend assets)
FROM node:20-alpine AS node_builder

WORKDIR /app

# Copy necessary files
COPY package.json package-lock.json ./
RUN npm install

# Copy remaining files and build assets
COPY . .
RUN npm run build

# Stage 3: Final production image
FROM php:8.3-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    libzip \
    libzip-dev \
    icu-dev \
    libpng \
    libpng-dev \
    libjpeg-turbo \
    oniguruma \
    icu-libs \
    bash \
    openssl \
    shadow \
    && docker-php-ext-install pdo pdo_mysql zip intl opcache gd

# Set working directory
WORKDIR /var/www/html

# Copy Laravel application from php_builder
COPY --from=php_builder /var/www/html /var/www/html

# Copy built assets from node_builder
COPY --from=node_builder /app/public ./public
COPY --from=node_builder /app/node_modules ./node_modules

# Set permissions
RUN addgroup -g 1000 www && \
    adduser -G www -g www -s /bin/sh -D www && \
    chown -R www:www /var/www/html && \
    chmod -R 775 storage bootstrap/cache

USER www

EXPOSE 9000
CMD ["php-fpm"]
