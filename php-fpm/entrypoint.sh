#!/bin/bash
set -e

if [ ! -d "vendor" ]; then
    composer install --optimize-autoloader
fi

exec /usr/local/sbin/php-fpm
