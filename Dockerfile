FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql gd

# Install Xdebug for code coverage and debugging
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Set working directory
WORKDIR /var/www/html

# Change ownership
RUN chown -R www-data:www-data /var/www/html
