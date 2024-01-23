<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use GuzzleHttp\Client;
use Shopware\Core\Framework\Store\Services\InstanceService;

final class PlatformDataProvider
{
    public function __construct(
        private readonly InstanceService $instanceService,
        private readonly Client $client,
        private readonly VersionFilterService $versionFilterService
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

        $versions = $this->versionFilterService->removeReleaseCandidates($versions);

        return $versions[0];
    }
}
