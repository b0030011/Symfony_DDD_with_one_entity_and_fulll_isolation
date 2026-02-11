#!/bin/bash
set -e

if [ ! -d "vendor" ]; then
    composer install --optimize-autoloader
fi

frankenphp php-server --root /app/public public/index.php
