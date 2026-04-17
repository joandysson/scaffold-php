FROM php:8.2-apache

ARG APP_STAGE=local
ARG XDEBUG_MODE
ARG XDEBUG_START_WITH_REQUEST
ARG XDEBUG_CLIENT_HOST
ARG XDEBUG_CLIENT_PORT

ENV APP_STAGE=${APP_STAGE}

# Install required packages and PHP extensions
RUN set -eux; \
    apt-get update; \
    apt-get install -y git curl unzip libicu-dev libzip-dev libssl-dev pkg-config; \
    docker-php-ext-install pdo pdo_mysql intl zip; \
    rm -rf /var/lib/apt/lists/*

# Install and enable MongoDB extension
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Install Xdebug only outside production images
RUN if [ "$APP_STAGE" != "prod" ]; then \
        pecl install xdebug-3.4.5; \
        docker-php-ext-enable xdebug; \
        { \
            echo "zend_extension=xdebug.so"; \
            echo "xdebug.mode=$XDEBUG_MODE"; \
            echo "xdebug.start_with_request=$XDEBUG_START_WITH_REQUEST"; \
            echo "xdebug.client_host=$XDEBUG_CLIENT_HOST"; \
            echo "xdebug.client_port=$XDEBUG_CLIENT_PORT"; \
        } > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi

# Install Composer
COPY --from=composer:2.7.4 /usr/bin/composer /usr/local/bin/composer

# Enable Apache modules
RUN a2enmod rewrite headers

WORKDIR /var/www/html

# Copy application files and install PHP dependencies
COPY . /var/www/html

RUN set -eux; \
    if [ "$APP_STAGE" = "prod" ]; then \
        composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress; \
    else \
        composer install --no-interaction --prefer-dist --no-progress; \
    fi

# Set permissions for the application directory
RUN mkdir -p /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage
