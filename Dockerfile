# Use Ubuntu as base image
FROM ubuntu:22.04

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive

# Install basic packages
RUN apt-get update && apt-get install -y \
    apache2 \
    mysql-client \
    php \
    php-cli \
    php-mysql \
    php-xml \
    php-mbstring \
    php-curl \
    php-zip \
    php-intl \
    php-bcmath \
    php-soap \
    php-gd \
    php-common \
    php-opcache \
    libapache2-mod-php \
    curl \
    git \
    unzip \
    sudo \
    && apt-get clean

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Setup Apache
RUN a2enmod rewrite

# Copy Magento source code
COPY src/ /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose HTTP
EXPOSE 80

# Start Apache in foreground
CMD ["apachectl", "-D", "FOREGROUND"]
