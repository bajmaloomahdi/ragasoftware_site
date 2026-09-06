FROM php:8.3-fpm-alpine

# System deps for PHP extensions
RUN apk add --no-cache \
        git \
        unzip \
        icu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mysqli \
        gd \
        intl \
        zip \
        exif \
        bcmath \
        opcache \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Sensible PHP defaults for local dev + uploads
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/html

# Run as the same uid as the host user to avoid permission issues on bind mounts
ARG UID=1000
ARG GID=1000
RUN addgroup -g ${GID} app 2>/dev/null || true \
    && adduser -u ${UID} -G app -s /bin/sh -D app 2>/dev/null || true
USER app

EXPOSE 9000
CMD ["php-fpm"]
