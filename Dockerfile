FROM php:8.3-fpm

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        bash \
        git \
        libzip-dev \
        unzip \
        zip \
        nodejs \
        npm \
    && docker-php-ext-install pdo_mysql mysqli zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

ENV NODE_OPTIONS=--openssl-legacy-provider
RUN npm install && npm run build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

CMD ["sh", "-c", "php artisan optimize:clear && php artisan migrate --force && php -S 0.0.0.0:8080 -t public"]
