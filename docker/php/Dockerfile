# PHP-FPM 8.3 with common Laravel extensions + Composer
FROM php:8.3-fpm-alpine

# System packages
RUN apk add --no-cache \
    bash git unzip icu-dev libzip-dev oniguruma-dev autoconf g++ make \
    libsodium-dev openssl-dev linux-headers


#RUN apt-get install -y libsodium-dev pkg-config

# PHP Extensions
RUN docker-php-ext-configure intl \
 && docker-php-ext-install -j$(nproc) pdo_mysql mbstring zip intl pcntl

# PECL extensions redis
RUN pecl install redis 
RUN docker-php-ext-enable redis
#RUN pecl install sodium
RUN docker-php-ext-enable sodium


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




# Ensure correct permissions
RUN chown -R www-data:www-data /var/www/html

# Switch to the non-privileged user to run the application
USER www-data

# Change the default command to run the entrypoint script
#ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
