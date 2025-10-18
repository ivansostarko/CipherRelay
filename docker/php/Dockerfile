# PHP-FPM 8.3 with common Laravel extensions + Composer
FROM php:8.3-fpm-alpine

# System packages
RUN apk add --no-cache \
    bash git unzip icu-dev libzip-dev oniguruma-dev autoconf g++ make \
    libsodium-dev openssl-dev linux-headers

# PHP Extensions
RUN docker-php-ext-configure intl \
 && docker-php-ext-install -j$(nproc) pdo_mysql mbstring zip intl pcntl

# PECL extensions (redis, sodium)
RUN pecl install redis \
 && docker-php-ext-enable redis \
 && echo "yes" | pecl install sodium \
 && docker-php-ext-enable sodium

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP config (tune as needed)
ENV PHP_MEMORY_LIMIT=512M \
    PHP_UPLOAD_MAX_FILESIZE=64M \
    PHP_POST_MAX_SIZE=64M

RUN { \
      echo "memory_limit=${PHP_MEMORY_LIMIT}"; \
      echo "upload_max_filesize=${PHP_UPLOAD_MAX_FILESIZE}"; \
      echo "post_max_size=${PHP_POST_MAX_SIZE}"; \
      echo "expose_php=0"; \
    } > /usr/local/etc/php/conf.d/99-cipherrelay.ini

WORKDIR /var/www/html

# www-data user may have uid/gid 82; adjust permissions in your host as needed.
# You can uncomment the lines below if you want to run as non-root custom user
# RUN addgroup -g 1000 app && adduser -D -u 1000 -G app app
# USER app

CMD ["php-fpm"]
