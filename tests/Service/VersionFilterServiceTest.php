<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Tests\Service;

use MobilisticsGmbH\MamoConnector\Service\VersionFilterService;
use PHPUnit\Framework\TestCase;

class VersionFilterServiceTest extends TestCase
{
    public function testRemoveRCDoesNotChangeWhenNoRCVersions(): void
    {
        $versions = ['6.4.20.2', '6.5.8.2'];

        $filteredVersions = $this->getFilterVersionService()->removeReleaseCandidates($versions);
        static::assertSame($versions, $filteredVersions);
    }

    public function testRemoveRCRemovesRCVersions(): void
    {
        $versions = ['6.4.20.2', '6.5.8.2', '6.6.0.0-rc1'];

        $filteredVersions = $this->getFilterVersionService()->removeReleaseCandidates($versions);
        static::assertSame(['6.4.20.2', '6.5.8.2'], $filteredVersions);
    }

    private function getFilterVersionService(): VersionFilterService
    {
        return new VersionFilterService();
    }
}
