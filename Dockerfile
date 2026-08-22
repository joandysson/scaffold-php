FROM php:8.5-fpm-alpine AS vendor

ARG APP_STAGE=local

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

COPY --from=composer:2.9.8 /usr/bin/composer /usr/local/bin/composer

RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev; \
    apk add --no-cache git icu-libs libzip unzip; \
    docker-php-ext-install pdo_mysql intl zip

COPY composer.json composer.lock ./
COPY tests ./tests

RUN set -eux; \
    if [ "$APP_STAGE" = "prod" ] || [ "$APP_STAGE" = "production" ]; then \
        composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-progress; \
    else \
        composer install --no-interaction --prefer-dist --no-progress; \
    fi; \
    composer clear-cache

FROM php:8.5-fpm-alpine

ARG APP_STAGE=local
ARG XDEBUG_MODE=off
ARG XDEBUG_START_WITH_REQUEST=no
ARG XDEBUG_CLIENT_HOST=host.docker.internal
ARG XDEBUG_CLIENT_PORT=9003

ENV APP_STAGE=${APP_STAGE}
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev linux-headers; \
    apk add --no-cache icu-libs libzip nginx; \
    docker-php-ext-install pdo_mysql intl zip; \
    if [ "$APP_STAGE" = "local" ]; then \
        pecl install xdebug; \
        docker-php-ext-enable xdebug; \
        { \
            echo "zend_extension=xdebug.so"; \
            echo "xdebug.mode=$XDEBUG_MODE"; \
            echo "xdebug.start_with_request=$XDEBUG_START_WITH_REQUEST"; \
            echo "xdebug.client_host=$XDEBUG_CLIENT_HOST"; \
            echo "xdebug.client_port=$XDEBUG_CLIENT_PORT"; \
        } > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi; \
    apk del .build-deps; \
    mkdir -p /run/nginx

COPY --from=composer:2.9.8 /usr/bin/composer /usr/local/bin/composer
COPY --from=vendor /app/vendor ./vendor

COPY . .

COPY docker/config/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php/config/php.ini /usr/local/etc/php/php.ini
COPY docker/entrypoint.sh /usr/local/bin/scaffold-php-entrypoint

RUN set -eux; \
    chmod +x /usr/local/bin/scaffold-php-entrypoint; \
    mkdir -p /var/www/html/storage/cache /var/www/html/storage/logs; \
    chown -R www-data:www-data /var/www/html/storage /var/lib/nginx /run/nginx; \
    chmod -R ug+rwX /var/www/html/storage; \
    if [ "$APP_STAGE" != "local" ]; then \
        rm -f /usr/local/bin/composer; \
    fi

ENTRYPOINT ["scaffold-php-entrypoint"]
CMD ["nginx", "-g", "daemon off;"]
