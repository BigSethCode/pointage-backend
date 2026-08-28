FROM php:8.4-fpm AS php

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

COPY . .
RUN composer run-script post-autoload-dump --no-interaction

RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

FROM php:8.4-fpm

RUN apt-get update && apt-get install -y nginx libpng16-16t64 \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=php /usr/local/lib/php/ /usr/local/lib/php/
COPY --from=php /usr/local/etc/php/ /usr/local/etc/php/
COPY --from=php /app /app

WORKDIR /app

COPY nginx.conf /etc/nginx/sites-available/default
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

RUN mkdir -p /var/run/nginx && chown -R www-data:www-data /var/run/nginx
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

EXPOSE 80

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
CMD ["docker-entrypoint.sh"]
