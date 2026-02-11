FROM dunglas/frankenphp:1-php8.4

RUN --mount=type=bind,from=mlocati/php-extension-installer:2,source=/usr/bin/install-php-extensions,target=/usr/local/bin/install-php-extensions \
     install-php-extensions @composer-2 zip gd pdo_pgsql pdo_mysql intl sockets xdebug

WORKDIR /app

COPY entrypoint.sh /docker-entrypoint.d/entrypoint.sh
RUN chmod +x /docker-entrypoint.d/entrypoint.sh

CMD ["/docker-entrypoint.d/entrypoint.sh"]
