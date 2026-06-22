FROM php:8.4-fpm

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev unzip \
    && docker-php-ext-install pdo_mysql opcache \
    && rm -rf /var/lib/apt/lists/*

RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.revalidate_freq=0'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# Static pool: workers never restart, OPcache stays warm between requests
RUN { \
    echo '[www]'; \
    echo 'pm = static'; \
    echo 'pm.max_children = 8'; \
    echo 'pm.max_requests = 0'; \
} >> /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html

# Bake vendor into image so the anonymous volume trick can shadow the slow host mount
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Dev mode: clear caches on container start so route/config/view edits show up
# without a rebuild or restart (runs after volumes are mounted).
RUN printf '#!/bin/sh\ncd /var/www/html\nphp artisan package:discover --ansi 2>/dev/null || true\nphp artisan config:clear 2>/dev/null || true\nphp artisan route:clear  2>/dev/null || true\nphp artisan view:clear   2>/dev/null || true\nexec "$@"\n' \
    > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
