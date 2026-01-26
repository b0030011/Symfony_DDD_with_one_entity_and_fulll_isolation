FROM php:8.4-fpm
ARG ANTISLEEP_ENV=local
ARG PHP_INI_ENV=production

# Копируем конфиги
COPY ./php.ini-$PHP_INI_ENV "$PHP_INI_DIR/php.ini"
COPY ./www.conf "/usr/local/etc/php-fpm.d/www.conf"

RUN apt-get update && apt-get install -y \
        mc \
        htop \
        libzip-dev \
        libpng-dev \
        libpq-dev \
        supervisor

RUN docker-php-ext-install pdo_pgsql pdo_mysql gd zip pcntl bcmath

# Копируем конфигурации supervisor
COPY ./supervisor/ /etc/supervisor/

# Убедимся, что директория supervisor существует и имеет правильные права
RUN mkdir -p /var/run/supervisor && chmod 755 /var/run/supervisor

RUN if [ "$ANTISLEEP_ENV" = "local" ]; then pecl install -f xdebug && docker-php-ext-enable xdebug ; fi

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin

WORKDIR /app

RUN usermod -u 1000 www-data

#CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
CMD ["/usr/local/sbin/php-fpm"]
