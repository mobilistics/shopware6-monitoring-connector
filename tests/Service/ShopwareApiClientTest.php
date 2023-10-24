<?php

namespace MobilisticsGmbH\MamoConnector\Tests\Service;

use MobilisticsGmbH\MamoConnector\Service\ShopwareApiClient;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Store\Services\InstanceService;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ShopwareApiClientTest extends TestCase
{
    use IntegrationTestBehaviour;

    private function getInstanceService(): InstanceService
    {
        /** @var InstanceService $instanceService */
        $instanceService = $this->getContainer()->get(InstanceService::class);

        return $instanceService;
    }

    /**
     * @param MockResponse[] $responses
     * @return HttpClientInterface
     */
    private function getMockedHttpClient(array $responses): HttpClientInterface
    {
        return new MockHttpClient($responses);
    }

    private function getMockApiClient(array $responses): ShopwareApiClient
    {
        return new ShopwareApiClient(
            $this->getInstanceService(),
            $this->getMockedHttpClient($responses),
        );
    }

    public function testEmptryReponseBody(): void
    {
        $client = $this->getMockApiClient([
            new MockResponse(),
        ]);

        $result = $client->getUpdatableVersions([]);
        static::assertSame([], $result);
    }

    public function testEmptyResponseFromApi(): void
    {
        $client = $this->getMockApiClient([
            new MockResponse('{"data": [], "totalCount": 0 }'),
        ]);

        $result = $client->getUpdatableVersions([]);
        static::assertSame([], $result);
    }

    public function testPluginResponsesFromApi(): void
    {
        $responses = [
            new MockResponse(<<<JSON
{
    "data": [
        {
            "name": "MobiExamplePlugin",
            "label": "Example Plugin",
            "iconPath": "https://sbp-plugin-images.s3.eu-west-1.amazonaws.com/phpK3DxI0",
            "version": "1.0.0",
            "changelog": "changelog",
            "releaseDate": "2023-10-19T08:19:19+02:00",
            "integrated": true
        },
        {
            "name": "MobiSecondPlugin",
            "label": "Second Testing Plugin",
            "iconPath": "https://sbp-plugin-images.s3.eu-west-1.amazonaws.com/phpK3DxI0",
            "version": "2.0.0",
            "changelog": "changelog",
            "releaseDate": "2023-10-19T08:19:19+02:00",
            "integrated": true
        }
    ],
    "totalCount": 2
}
JSON,
            ),
        ];

        $client = $this->getMockApiClient($responses);
        $result = $client->getUpdatableVersions([]);

        static::assertCount(2, $result);
        static::assertSame('MobiExamplePlugin', $result[0]->name);
        static::assertSame('1.0.0', $result[0]->version);
        static::assertSame('MobiSecondPlugin', $result[1]->name);
        static::assertSame('2.0.0', $result[1]->version);
    }
}
