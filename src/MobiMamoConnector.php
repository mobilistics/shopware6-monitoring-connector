<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector;

use Shopware\Core\Framework\Plugin;

class MobiMamoConnector extends Plugin
{
    final public const PLUGIN_IDENTIFIER = 'MobiMamoConnector';

    public function executeComposerCommands(): bool
    {
        return true;
    }
}
