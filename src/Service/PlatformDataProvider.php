<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use Shopware\Core\Framework\Store\Services\InstanceService;
use Shopware\Core\Framework\Update\Services\ApiClient;

final class PlatformDataProvider
{
    public function __construct(
        private readonly InstanceService $instanceService,
        private readonly ApiClient $apiClient
    ) {
    }

    public function getCurrentPlatformVersion(): string
    {
        return $this->instanceService->getShopwareVersion();
    }

    public function getLatestPlatformVersion(): string
    {
        // TODO: Do we want to trust shopware here? Its possible (and maybe wanted by agencies)
        //       To fake a version here to prevent updates in the administration.
        $latestVersion = $this->apiClient->checkForUpdates();
        return $latestVersion->version;
    }
}
