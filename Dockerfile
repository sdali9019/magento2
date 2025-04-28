# Use Ubuntu base
FROM ubuntu:22.04

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive

# Install PHP, Composer, and required extensions
RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-common php-mysql php-gd php-xml php-mbstring php-curl php-zip php-bcmath php-soap unzip curl git && \
    apt-get clean

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy Magento 2 code into container
COPY . .

# Install Magento dependencies
RUN composer install --no-interaction --optimize-autoloader

# Run Magento setup and deployment commands
RUN php bin/magento setup:install \
    --base-url=http://localhost \
    --db-host=localhost \
    --db-name=magento \
    --db-user=root \
    --db-password=root \
    --admin-firstname=admin \
    --admin-lastname=admin \
    --admin-email=admin@example.com \
    --admin-user=admin \
    --admin-password=Admin123 \
    --language=en_US \
    --currency=USD \
    --timezone=America/Chicago \
    --use-rewrites=1 || true

RUN php bin/magento setup:upgrade || true
RUN php bin/magento setup:di:compile || true
RUN php bin/magento setup:static-content:deploy -f || true

# Give permissions (important for Magento)
RUN chmod -R 777 var pub generated

# Expose HTTP port
EXPOSE 80

# Start Apache when container runs
CMD ["apache2ctl", "-D", "FOREGROUND"]