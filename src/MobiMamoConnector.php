<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector;

use Shopware\Core\Framework\Plugin;

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

class MobiMamoConnector extends Plugin
{
    final public const PLUGIN_IDENTIFIER = 'MobiMamoConnector';

    final public const CONFIG_KEY_SECRET = self::PLUGIN_IDENTIFIER . '.config.secret';
}
