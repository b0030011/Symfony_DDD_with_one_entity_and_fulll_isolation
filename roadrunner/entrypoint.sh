#!/bin/bash
set -e

if [ ! -d "vendor" ]; then
    composer install --optimize-autoloader
    composer require baldinof/roadrunner-bundle:^3.0 --with-all-dependencies
    composer require spiral/roadrunner-cli --dev --with-all-dependencies
fi

rr serve -c /docker-entrypoint.d/rr.yaml
