#!/usr/bin/env sh

/opt/bin/plugin-uploader ext:prepare "/plugins/${PLUGIN_NAME}"
start-mysql
export PROJECT_ROOT=/opt/shopware
/opt/shopware/bin/console plugin:refresh
/opt/shopware/bin/console plugin:install --activate -c "${PLUGIN_NAME}"
cd "/opt/shopware/custom/plugins/${PLUGIN_NAME}" || exit 1
composer dump-autoload --dev
php /opt/shopware/vendor/bin/phpunit --configuration ./phpunit.xml
