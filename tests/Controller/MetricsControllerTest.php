<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Tests\Controller;

use MobilisticsGmbH\MamoConnector\MobiMamoConnector;
use MobilisticsGmbH\MamoConnector\Tests\Support\StorefrontControllerTestBehaviour;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class MetricsControllerTest extends TestCase
{
    use IntegrationTestBehaviour;
    use StorefrontControllerTestBehaviour;

    private const TESTING_SECRET = "testing-secret";

    public function testMetricsAction(): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        $systemConfigService->set(MobiMamoConnector::CONFIG_KEY_SECRET, self::TESTING_SECRET);

        $metrics = $this->request('GET', 'mamo-connector/metrics?unsecure&secret=testing-secret', []);

        $content = $metrics->getContent();
        static::assertNotFalse($content);
        static::assertEquals(200, $metrics->getStatusCode());

        static::assertStringContainsString('mamo_shopware6_platform', $content);
    }

    public function testFailWithInvalidLegacySecret(): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        $systemConfigService->set(MobiMamoConnector::CONFIG_KEY_SECRET, self::TESTING_SECRET);

        $metrics = $this->request('GET', 'mamo-connector/metrics?unsecure', []);
        $content = $metrics->getContent();

        static::assertNotFalse($content);
        static::assertEquals(403, $metrics->getStatusCode());
    }

    public function testFailWithInvalidHmacHeader(): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        $systemConfigService->set(MobiMamoConnector::CONFIG_KEY_SECRET, self::TESTING_SECRET);

        $metrics = $this->request('GET', 'mamo-connector/metrics', [
            "header" => [
                "Hmac" => "dummy"
            ]
        ]);
        $content = $metrics->getContent();

        static::assertNotFalse($content);
        static::assertEquals(401, $metrics->getStatusCode());
    }

    public function testValidRequestWithHmac(): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        $systemConfigService->set(MobiMamoConnector::CONFIG_KEY_SECRET, self::TESTING_SECRET);

        $content = '{"validateTime":' . mktime(0) . '}';

        $metrics = $this->request('GET', 'mamo-connector/metrics', [], [], [
            "HTTP_Hmac" => hash_hmac('sha256', '{"validateTime":' . mktime(0) . '}', self::TESTING_SECRET),
        ], $content);
        $content = $metrics->getContent();

        static::assertNotFalse($content);
        static::assertEquals(200, $metrics->getStatusCode());
        static::assertStringContainsString('mamo_shopware6_platform', $content);
    }

    public function testHmacMissingBody(): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        $systemConfigService->set(MobiMamoConnector::CONFIG_KEY_SECRET, self::TESTING_SECRET);

        $content = '{"validateTime":' . mktime(0) . '}';

        $metrics = $this->request('GET', 'mamo-connector/metrics', [], [], [
            "HTTP_Hmac" => hash_hmac('sha256', '{"validateTime":' . mktime(0) . '}', self::TESTING_SECRET),
        ], ""); // <- missing request body
        $content = $metrics->getContent();

        static::assertNotFalse($content);
        static::assertEquals(400, $metrics->getStatusCode());
    }

    public function testHmacMismatch(): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        $systemConfigService->set(MobiMamoConnector::CONFIG_KEY_SECRET, self::TESTING_SECRET);

        $content = '{"validateTime":' . mktime(0) . '}';

        $metrics = $this->request('GET', 'mamo-connector/metrics', [], [], [
            "HTTP_Hmac" => hash_hmac('sha256', '{"validateTime":' . mktime(0) . '}', self::TESTING_SECRET),
        ], "hmac-mismatch"); // <- missing request body
        $content = $metrics->getContent();

        static::assertNotFalse($content);
        static::assertEquals(403, $metrics->getStatusCode());
    }

    /**
     * NOTE: This can only happen, when we change our config template or Shopware itself screws up.
     */
    public function testConfigurationSecretIsNotAString(): void
    {
        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $this->getContainer()->get(SystemConfigService::class);

        // Screw up the configuration
        $systemConfigService->set(MobiMamoConnector::CONFIG_KEY_SECRET, false);

        // Regular controller access
        $metrics = $this->request('GET', 'mamo-connector/metrics?unsecure', []);
        $content = $metrics->getContent();

        static::assertNotFalse($content);
        static::assertEquals(500, $metrics->getStatusCode());
    }
}
