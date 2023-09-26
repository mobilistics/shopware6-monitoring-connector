FROM php:8.1

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer:2.1 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions \
		gd-stable \
		curl-stable \
		imagick-stable \
		mysqli-stable \
		memcached-stable \
		intl-stable \
		apcu-stable \
		redis-stable \
		mbstring-stable \
		xml-stable \
		soap-stable \
		zip-stable \
		opcache-stable \
		exif-stable \
		gettext-stable \
        pdo_mysql-stable
