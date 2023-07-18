<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use MobilisticsGmbH\MamoConnector\Dto\Plugin;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\PluginEntity;
use Shopware\Core\Framework\Store\Services\InstanceService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ExtensionDataProvider
{
    public function __construct(
        private readonly EntityRepository $pluginRepository,
        private readonly HttpClientInterface $client,
        private readonly InstanceService $instanceService,
    ) {
    }

    /**
     * @return array<Plugin>
     */
    public function loadExtensionData(): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', '1'));

        /** @var PluginEntity[] $plugins */
        $plugins = $this->pluginRepository->search($criteria, Context::createDefaultContext());

        $pluginList = [];

        foreach ($plugins as $plugin) {
            $pluginList[] = [
                'name' => $plugin->getName(),
                'version' => $plugin->getVersion(),
            ];
        }

        $response = $this->client->request(
            'POST',
            'https://api.shopware.com/swplatform/pluginupdates',
            [
                'query' => [
                    'language' => 'en-GB',
                    'domain' => '',
                    'shopwareVersion' => $this->instanceService->getShopwareVersion(),
                ],
                'json' => [
                    'plugins' => $pluginList,
                ],
            ]
        );

        $shopwareExtensions = json_decode($response->getContent());

        $versions = [];
        foreach ($shopwareExtensions->data as $extension) {
            $versions[$extension->name] = $extension->version;
        }

        $result = [];

        foreach ($pluginList as $plugin) {
            $name = $plugin['name'];
            $version = $plugin['version'];
            $latestVersion = isset($versions[$plugin['name']]) ? $versions[$plugin['name']] : $plugin['version'];

            $result[] = new Plugin($name, $version, $latestVersion);
        }

        return $result;
    }
}
