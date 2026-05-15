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

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

CMD ["sh", "-c", "ls -la vendor/autoload.php && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]