<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Tests\Controller;

use MobilisticsGmbH\MamoConnector\MobiMamoConnector;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Test\Controller\StorefrontControllerTestBehaviour;

class MetricsControllerTest extends TestCase
{
    use IntegrationTestBehaviour;
    use StorefrontControllerTestBehaviour;

    public function testMetricsAction(): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        $systemConfigService->set(MobiMamoConnector::CONFIG_KEY_SECRET, 'testing-secret');

        $metrics = $this->request('GET', 'mamo-connector/metrics?unsecure&secret=testing-secret', []);

        $content = $metrics->getContent();
        static::assertNotFalse($content);
        static::assertEquals(200, $metrics->getStatusCode());

        static::assertStringContainsString('mamo_shopware6_platform', $content);
    }

    public function testFailWhenNoSecretProvided(): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        $systemConfigService->set(MobiMamoConnector::CONFIG_KEY_SECRET, 'testing-secret');

        $metrics = $this->request('GET', 'mamo-connector/metrics', []);
        $content = $metrics->getContent();

        static::assertNotFalse($content);
        static::assertEquals(401, $metrics->getStatusCode());
    }
}
