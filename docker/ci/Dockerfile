# Sityos CI Image
# PHP 8.3-cli + Composer 2 + Node 20 + git
#
# Mirrors the extensions in docker/php/Dockerfile so that CI and production
# use the same PHP surface. Not used as the runtime server image.
#
# Usage (local artifact build):
#   docker build -t sityos-ci docker/ci/
#   docker run --rm -v $(pwd):/app -w /app sityos-ci bash scripts/build-artifact.sh

# Stage 1: Build PHP extensions that require compilation (redis via pecl)
FROM php:8.3-cli-alpine AS php-builder

RUN apk add --no-cache autoconf g++ make libc-dev linux-headers \
    && pecl install redis \
    && docker-php-ext-enable redis

# Stage 2: Runtime CI image
FROM php:8.3-cli-alpine

LABEL maintainer="Sityos Automate CI <dev@sityos.com>"

# System packages
RUN apk add --no-cache \
    bash \
    git \
    curl \
    unzip \
    # GD dependencies (matches docker/php/Dockerfile)
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libavif-dev \
    freetype-dev \
    # Other PHP extension dependencies
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    # Node 20 via apk
    nodejs \
    npm

# Verify Node version meets requirement (>=20)
RUN node --version | grep -E '^v2[0-9]\.' \
    || { echo "ERROR: Node 20+ required. Found: $(node --version)" >&2; exit 1; }

# PHP extensions (same set as docker/php/Dockerfile)
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-avif \
    && docker-php-ext-install -j"$(nproc)" \
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
COPY --from=php-builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-builder /usr/local/etc/php/conf.d/docker-php-ext-redis.ini \
                        /usr/local/etc/php/conf.d/

# Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Verify tools
RUN php --version && composer --version && node --version && npm --version && git --version

WORKDIR /app
