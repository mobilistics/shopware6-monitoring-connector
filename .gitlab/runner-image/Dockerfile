FROM php:8.3

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
    gd \
    curl \
    mysqli \
    memcached \
    intl \
    redis \
    mbstring \
    xml \
    soap \
    zip \
    opcache \
    exif \
    gettext \
    pdo_mysql
