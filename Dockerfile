FROM php:8.3-fpm

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        bash \
        git \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-install pdo_mysql mysqli zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN usermod -u 1000 www-data \
    && groupmod -g 1000 www-data

CMD ["php-fpm"]
