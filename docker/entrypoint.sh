#!/bin/sh
set -eu

STORAGE_ROOT="/var/www/html/storage"

mkdir -p "${STORAGE_ROOT}/cache" "${STORAGE_ROOT}/logs"
chown -R www-data:www-data "${STORAGE_ROOT}"
chmod -R ug+rwX "${STORAGE_ROOT}"

if [ "${1:-}" = "nginx" ]; then
    php-fpm -D
fi

exec docker-php-entrypoint "$@"
