<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector;

use Shopware\Core\Framework\Plugin;

class MobiMamoConnector extends Plugin
{
    final public const PLUGIN_IDENTIFIER = 'MobiMamoConnector';

    final public const CONFIG_KEY_SECRET = self::PLUGIN_IDENTIFIER . '.config.secret';

    public function executeComposerCommands(): bool
    {
        return true;
    }
}
