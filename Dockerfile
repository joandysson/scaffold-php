FROM php:8.2-apache

# Install basic PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install required packages to compile PHP extensions
RUN set -eux; apt-get update; apt-get install -y git curl libicu-dev libzip-dev libssl-dev pkg-config

# Install intl and zip extensions
RUN docker-php-ext-install intl zip

# Install and enable MongoDB extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:2.7.4 /usr/bin/composer /usr/local/bin/composer

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && XDEBUG_SO=$(find /usr/local/lib/php/extensions/ -name xdebug.so) \
    && echo "zend_extension=$XDEBUG_SO" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log_level=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Enable Apache modules
RUN a2enmod rewrite headers

# Restart Apache (can be removed if using default ENTRYPOINT)
RUN service apache2 restart

# Copy application files
COPY . /var/www/html

# Set permissions for the application directory
RUN mkdir -p /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage
