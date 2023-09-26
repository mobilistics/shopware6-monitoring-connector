<?php

namespace MobilisticsGmbH\MamoConnector\Tests\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MobilisticsGmbH\MamoConnector\Service\PlatformDataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Shopware\Core\Framework\Store\Services\InstanceService;
use Shopware\Core\Framework\Test\TestCaseBase\KernelTestBehaviour;

class PlatformDataProviderTest extends TestCase
{
    use KernelTestBehaviour;

    private const SHOPWARE_VERSION = "0.0.0.0";
    private const INSTANCE_ID = "1234567890";

    private function getMockHttpClient(): ClientInterface
    {
        $mock = new MockHandler([
            new Response(200, [], '[]'),
        ]);

        $handlerStack = HandlerStack::create($mock);
        return new Client(['handler' => $handlerStack]);
    }

    public function getMockInstanceService(): InstanceService
    {
        return new InstanceService(self::SHOPWARE_VERSION, self::INSTANCE_ID);
    }

    public function testGetCurrentPlatformVersion(): void
    {
        $platformDataProvider = new PlatformDataProvider($this->getMockInstanceService(), $this->getMockHttpClient());
        $currentPlatformVersion = $platformDataProvider->getCurrentPlatformVersion();

        static::assertEquals(self::SHOPWARE_VERSION, $currentPlatformVersion);
        static::assertNotEmpty($currentPlatformVersion);
    }
}
