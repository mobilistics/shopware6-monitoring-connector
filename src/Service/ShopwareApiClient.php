<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use MobilisticsGmbH\MamoConnector\Dto\ShopwareApi\Plugin;
use Shopware\Core\Framework\Store\Services\InstanceService;
use Symfony\Component\HttpFoundation\Request;

class ShopwareApiClient
{
    private const SHOPWARE_API_DOMAIN = 'https://api.shopware.com';

    public function __construct(
        private InstanceService $instanceService,
        private Client $client,
    ) {
    }

    /**
     * @param Plugin[] $plugins
     * @return Plugin[]
     * @throws GuzzleException
     */
    public function getUpdatableVersions(array $plugins): array
    {
        $response = $this->client->request(
            Request::METHOD_POST,
            self::SHOPWARE_API_DOMAIN . '/swplatform/pluginupdates',
            [
                'query' => $this->getQuery(),
                'json' => [
                    'plugins' => $plugins,
                ],
            ]
        );

        $body = json_decode($response->getBody()->getContents());
        if (! isset($body->data)) {
            return [];
        }

        // Convert the response to an array of Plugin objects
        return array_map(static function ($plugin) {
            return new Plugin($plugin->name, $plugin->version);
        }, $body->data);
    }

    /**
     * @return array<string, string>
     */
    private function getQuery(): array
    {
        return [
            'language' => 'en-GB',
            'domain' => '',
            'shopwareVersion' => $this->instanceService->getShopwareVersion(),
        ];
    }
}
