<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Tests\Service;

use MobilisticsGmbH\MamoConnector\Dto\ShopwareApi\Plugin as ShopwareApiPlugin;
use MobilisticsGmbH\MamoConnector\Service\PluginMerger;
use PHPUnit\Framework\TestCase;

class PluginMergerTest extends TestCase
{
    public function testMergePlugins(): void
    {
        $databasePlugins = [
            new ShopwareApiPlugin('plugin1', '1.0.0'),
            new ShopwareApiPlugin('plugin2', '1.1.0'),
            new ShopwareApiPlugin('plugin3', '1.1.1'),
        ];

        $updatablePlugins = [
            new ShopwareApiPlugin('plugin1', '2.0.0'),
            new ShopwareApiPlugin('plugin2', '1.1.1'),
            new ShopwareApiPlugin('plugin3', '1.1.2'),
        ];

        $pluginMerger = new PluginMerger();
        $result = $pluginMerger->merge($databasePlugins, $updatablePlugins);

        $this->assertCount(3, $result);
        $this->assertEquals('2.0.0', $result[0]->latestVersion);
        $this->assertEquals('1.0.0', $result[0]->version);
        $this->assertEquals('1.1.1', $result[1]->latestVersion);
        $this->assertEquals('1.1.0', $result[1]->version);
        $this->assertEquals('1.1.2', $result[2]->latestVersion);
        $this->assertEquals('1.1.1', $result[2]->version);
    }
}
