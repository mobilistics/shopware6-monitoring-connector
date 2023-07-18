<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use MobilisticsGmbH\MamoConnector\Dto\Plugin;
use MobilisticsGmbH\MamoConnector\Dto\ShopwareApi\Plugin as ShopwareApiPlugin;

class PluginMerger
{
    /**
     * @param ShopwareApiPlugin[] $databasePlugins
     * @param ShopwareApiPlugin[] $updatablePlugins
     * @return Plugin[]
     */
    public function merge(array $databasePlugins, array $updatablePlugins): array
    {
        $updatableVersionMap = $this->mapPluginNamesWithVersions($updatablePlugins);
        $result = [];

        foreach ($databasePlugins as $plugin) {
            $name = $plugin->name;
            $version = $plugin->version;
            $latestVersion = $updatableVersionMap[$plugin->name] ?? $plugin->version;

            $result[] = new Plugin($name, $version, $latestVersion);
        }

        return $result;
    }

    /**
     * Return the plugin names as keys and the versions as values
     * @param ShopwareApiPlugin[] $plugins
     * @return array<string, string>
     */
    private function mapPluginNamesWithVersions(array $plugins): array
    {
        $result = [];

        foreach ($plugins as $plugin) {
            $result[$plugin->name] = $plugin->version;
        }

        return $result;
    }
}
