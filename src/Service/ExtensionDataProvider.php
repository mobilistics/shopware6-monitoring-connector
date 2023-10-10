<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use GuzzleHttp\Exception\GuzzleException;
use MobilisticsGmbH\MamoConnector\Dto\Plugin;
use MobilisticsGmbH\MamoConnector\Dto\ShopwareApi\Plugin as ShopwareApiPlugin;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\PluginEntity;

final class ExtensionDataProvider
{
    public function __construct(
        private readonly EntityRepository $pluginRepository,
        private readonly ShopwareApiClient $shopwareApiClient,
        private readonly PluginMerger $pluginMerger,
    ) {
    }

    /**
     * @return array<Plugin>
     * @throws GuzzleException
     */
    public function loadExtensionData(): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', '1'));

        /** @var PluginEntity[] $plugins */
        $plugins = $this->pluginRepository->search($criteria, Context::createDefaultContext());

        $databasePlugins = [];

        foreach ($plugins as $plugin) {
            $databasePlugins[] = new ShopwareApiPlugin(
                name: $plugin->getName(),
                version: $plugin->getVersion(),
            );
        }

        $updatablePlugins = $this->shopwareApiClient->getUpdatableVersions($databasePlugins);
        return $this->pluginMerger->merge($databasePlugins, $updatablePlugins);
    }
}
