#!/usr/bin/env sh

PLUGIN_PATH="/plugins/${PLUGIN_NAME}"
JWT_TEST_DIRECTORY="/opt/shopware/var/test/jwt"

start-mysql

cd "${PLUGIN_PATH}" || exit 1

composer install

mkdir -p "${JWT_TEST_DIRECTORY}"

cp /opt/shopware/config/jwt/*pem "${JWT_TEST_DIRECTORY}"

/opt/bin/phpunit --configuration "${PLUGIN_PATH}/phpunit.xml"
