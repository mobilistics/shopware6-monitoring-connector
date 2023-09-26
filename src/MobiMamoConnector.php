<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector;

use Shopware\Core\Framework\Plugin;

class MobiMamoConnector extends Plugin
{
    public const PLUGIN_IDENTIFIER = 'MobiMamoConnector';

    public const CONFIG_KEY_SECRET = self::PLUGIN_IDENTIFIER . '.config.secret';
}
