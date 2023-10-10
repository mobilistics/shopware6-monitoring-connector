<?php
declare(strict_types=1);

/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Shopware\Core\TestBootstrapper;

if (is_readable('/opt/share/shopware/tests/TestBootstrapper.php')) {
    // For Docker image: ghcr.io/friendsofshopware/platform-plugin-dev
    $testBootstrapper = require '/opt/share/shopware/tests/TestBootstrapper.php';
} else if (is_readable(__DIR__ . '/../vendor/shopware/platform/src/Core/TestBootstrapper.php')) {
    require __DIR__ . '/../vendor/shopware/platform/src/Core/TestBootstrapper.php';
} elseif (is_readable(__DIR__ . '/../vendor/shopware/core/TestBootstrapper.php')) {
    require __DIR__ . '/../vendor/shopware/core/TestBootstrapper.php';
} else {
    // vendored from platform, only use local TestBootstrapper if not already defined in platform
    require __DIR__ . '/TestBootstrapper.php';
}

$autoloadFile = dirname(__DIR__) . '/vendor/autoload.php';

if (!is_readable($autoloadFile)) {
    throw new RuntimeException('Could not find autoload.php in vendor/. Did you run composer install?');
}

$classLoader = require $autoloadFile;

return (new TestBootstrapper())
    ->setLoadEnvFile(true)
    ->setForceInstallPlugins(true)
    ->addActivePlugins('MobiMamoConnector')
    ->bootstrap()
    ->getClassLoader();

// docker run --rm -it -v "${PWD}:/plugins/MobiMamoConnector" ghcr.io/friendsofshopware/platform-plugin-dev:v6.4.0 sh -c 'start-mysql && cd /plugins/MobiMamoConnector && phpunit'
