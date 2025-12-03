ARG BASE_RUNTIME_IMAGE="dunglas/frankenphp:php8.5-alpine"
ARG COMPOSER_IMAGE="composer:2.9.2"

FROM ${COMPOSER_IMAGE} AS composer

FROM ${BASE_RUNTIME_IMAGE} as builder

RUN install-php-extensions \
    pcntl \
    gd \
    zip \
    exif

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY . /app

WORKDIR /app

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

FROM ${BASE_RUNTIME_IMAGE} as production

ARG OAUTH_PRIVATE_KEY
ARG OAUTH_PUBLIC_KEY

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app

RUN install-php-extensions \
    pcntl \
    pdo \
    pdo_pgsql \
    pdo_mysql \
    ctype \
    curl \
    dom \
    fileinfo \
    filter \
    gd \
    hash \
    mbstring \
    openssl \
    pcre \
    session \
    tokenizer \
    xml \
    zip \
    exif

# Custom config PHP ini
COPY deploy/php.ini /usr/local/etc/php/conf.d/

COPY --from=builder /app /app

RUN chmod +x /app/deploy/startup.sh

EXPOSE 8000
