FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN set -eux; apt-get update; apt-get install -y git

COPY --from=composer:2.7.4 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update; apt-get install curl -y; service apache2 restart;

# RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - && set -eux; \
#     apt-get update; \
#     apt-get install -y nodejs; \
#     npm install -g npm; \
#     npm install --global yarn; \
#     npm install --global gulp-cli;

COPY . /var/www/html

RUN a2enmod rewrite

RUN a2enmod headers && service apache2 restart
