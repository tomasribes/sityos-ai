# Stage 1: Build dependencies
FROM php:8.3-fpm-alpine AS builder

RUN apk add --no-cache \
    autoconf \
    g++ \
    make \
    libc-dev \
    && pecl install redis \
    && docker-php-ext-enable redis

# Stage 2: Runtime image
FROM php:8.3-fpm-alpine

LABEL maintainer="Sityos Automate <dev@sityos.com>"

# System dependencies
RUN apk add --no-cache \
    bash \
    git \
    curl \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libavif-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    linux-headers \
    su-exec \
    mysql-client

# PHP extensions required by Drupal 11
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-avif \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mbstring \
        xml \
        zip \
        intl \
        opcache \
        bcmath \
        exif

# Redis extension from builder
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/docker-php-ext-redis.ini /usr/local/etc/php/conf.d/

# PHP configuration
COPY php.ini /usr/local/etc/php/conf.d/sityos.ini

# Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Non-root user
RUN addgroup -g 1000 drupal \
    && adduser -u 1000 -G drupal -s /bin/bash -D drupal

USER drupal

RUN git config --global --add safe.directory /var/www/html

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
