<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Tests;

use MobilisticsGmbH\MamoConnector\MobiMamoConnector;
use PHPUnit\Framework\TestCase;

class MobiMamoConnectorTest extends TestCase
{
    public function testExecuteComposerCommandsIsSet(): void
    {
        $plugin = new MobiMamoConnector(true, '');
        static::assertTrue($plugin->executeComposerCommands());
    }
}
