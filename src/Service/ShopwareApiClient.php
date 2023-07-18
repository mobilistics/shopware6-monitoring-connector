<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use MobilisticsGmbH\MamoConnector\Dto\ShopwareApi\Plugin;
use Shopware\Core\Framework\Store\Services\InstanceService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShopwareApiClient
{
    private const SHOPWARE_API_DOMAIN = 'https://api.shopware.com';

    public function __construct(
        private readonly InstanceService $instanceService,
        private readonly HttpClientInterface $client,
    ) {
    }

    /**
     * @param Plugin[] $plugins
     * @return Plugin[]
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
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

        $body = json_decode($response->getContent());
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
