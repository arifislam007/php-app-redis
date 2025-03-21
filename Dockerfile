# Use PHP with Apache as base image
FROM php:8.2-apache

# Install Redis extension
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis

# Set the working directory
WORKDIR /var/www/html

# Copy application files
COPY ./app /var/www/html

# Enable Apache mod_rewrite
RUN a2enmod rewrite

EXPOSE 80

