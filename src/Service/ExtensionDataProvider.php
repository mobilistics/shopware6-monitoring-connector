<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use MobilisticsGmbH\MamoConnector\Dto\Plugin;
use MobilisticsGmbH\MamoConnector\Dto\ShopwareApi\Plugin as ShopwareApiPlugin;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\PluginEntity;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final readonly class ExtensionDataProvider
{
    public function __construct(
        private EntityRepository $pluginRepository,
        private ShopwareApiClient $shopwareApiClient,
        private PluginMerger $pluginMerger,
    ) {
    }

    /**
     * @return array<Plugin>
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
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
