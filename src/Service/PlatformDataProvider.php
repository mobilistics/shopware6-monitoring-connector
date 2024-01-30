<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use Shopware\Core\Framework\Store\Services\InstanceService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PlatformDataProvider
{
    public function __construct(
        private readonly InstanceService $instanceService,
        private readonly HttpClientInterface $client,
        private readonly VersionFilterService $versionFilterService
    ) {
    }

    public function getCurrentPlatformVersion(): string
    {
        return $this->instanceService->getShopwareVersion();
    }

    public function getLatestPlatformVersion(): string
    {
        /** @var non-empty-array<string> $versions */
        $versions = $this->client->request('GET', 'https://releases.shopware.com/changelog/index.json')->toArray();

        usort($versions, static function ($a, $b) {
            return version_compare($b, $a);
        });

        $versions = $this->versionFilterService->removeReleaseCandidates($versions);

        return $versions[0];
    }
}
