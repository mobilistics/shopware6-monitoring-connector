<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use GuzzleHttp\Client;
use Shopware\Core\Framework\Store\Services\InstanceService;

final class PlatformDataProvider
{
    public function __construct(
        private InstanceService $instanceService,
        private Client $client,
    ) {
    }

    public function getCurrentPlatformVersion(): string
    {
        return $this->instanceService->getShopwareVersion();
    }

    public function getLatestPlatformVersion(): string
    {
        $response = $this->client->request('GET', 'https://releases.shopware.com/changelog/index.json');

        /** @var non-empty-array<string> $versions */
        $versions = json_decode($response->getBody()->getContents());

        usort($versions, static function ($a, $b) {
            return version_compare($b, $a);
        });

        return $versions[0];
    }
}
