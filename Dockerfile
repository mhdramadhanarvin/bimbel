# Base image
FROM php:8.4-apache

# Set working directory in the container
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    g++ \
    curl \
    libzip-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpng-dev \
    libwebp-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configure GD extension
RUN docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/exclude/ \
    --with-webp=/usr/exclude/

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif bcmath zip intl gd \
   && docker-php-ext-enable intl gd

# Configure Apache
RUN echo "ServerName laravel-app.local" >> /etc/apache2/apache2.conf
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite headers

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js (latest LTS version)
RUN apt-get update && curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

# Copy the application files and set permissions
COPY --chown=www-data:www-data . /var/www/html
RUN rm -rf vendor node_modules

# Install PHP dependencies
RUN composer install

# Install Node.js dependencies and build assets
RUN npm install
RUN npm run build

# Set proper permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public
RUN php artisan storage:link
RUN php artisan migrate:fresh --seed

# Expose port 80
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
