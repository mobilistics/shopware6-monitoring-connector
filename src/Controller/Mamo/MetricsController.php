<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Controller\Mamo;

use MobilisticsGmbH\MamoConnector\MobiMamoConnector;
use MobilisticsGmbH\MamoConnector\Service\ExtensionDataProvider;
use MobilisticsGmbH\MamoConnector\Service\PlatformDataProvider;
use MobilisticsGmbH\MamoConnector\Service\RequestAuthorizationService;
use MobilisticsGmbH\MamoConnector\Utility\VersionUtility;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController extends StorefrontController
{
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly RequestAuthorizationService $requestAuthorizationService,
        private readonly ExtensionDataProvider $extensionDataProvider,
        private readonly LoggerInterface $logger,
        private readonly PlatformDataProvider $platformDataProvider,
    ) {
    }

    #[Route(path: '/mamo-connector/metrics', name: 'frontend.mamo-connector.metrics', defaults: [
        '_routeScope' => ['storefront'],
    ], methods: ['GET'])]
    public function indexAction(Request $request): Response
    {
        $this->verifyRequest($request);
        $secret = $this->getConfigurationSecret();

        $registry = new CollectorRegistry(new InMemory());
        $registry->getOrRegisterGauge('mamo', 'shopware6_platform', 'Shopware 6 Platform Version', ['latestVersion', 'currentVersion'])
            ->set(
                VersionUtility::convertVersionToInteger($this->platformDataProvider->getCurrentPlatformVersion()),
                [
                    'latestVersion' => $this->platformDataProvider->getLatestPlatformVersion(),
                    'currentVersion' => $this->platformDataProvider->getCurrentPlatformVersion(),
                ]
            );

        foreach ($this->extensionDataProvider->loadExtensionData() as $plugin) {
            $latestVersion = $plugin->latestVersion;
            $version = $plugin->version;

            $registry->getOrRegisterGauge('mamo', 'shopware6_plugin', 'Shopware 6 Plugin Version', ['name', 'latestVersion', 'currentVersion'])
                ->set(
                    VersionUtility::convertVersionToInteger($version),
                    [
                        'name' => $plugin->name,
                        'latestVersion' => $latestVersion,
                        'currentVersion' => $version,
                    ],
                );
        }

        $renderer = new RenderTextFormat();
        $result = $renderer->render($registry->getMetricFamilySamples());

        $headers = [
            'Content-Type' => RenderTextFormat::MIME_TYPE,
        ];

        if (! $request->query->has('unsecure')) {
            $headers['HMAC'] = hash_hmac('sha256', $result, $secret);
        }

        return new Response($result, 200, $headers);
    }

    /**
     * Verify that the given request is authorized to access the metrics endpoint.
     * Throws an HttpException with the appropriate status code, if the request is not authorized.
     */
    private function verifyRequest(Request $request): void
    {
        $secret = $this->getConfigurationSecret();

        // Handle legacy request with the secret in the query parameter.
        if ($request->query->has('unsecure')) {
            $this->validateSecretRequest($request, $secret);
            return;
        }

        $this->validateHmacRequest($request, $secret);
    }

    private function validateHmacRequest(Request $request, string $secret): void
    {
        $hmacHeader = $request->headers->get('Hmac');
        if (! $hmacHeader) {
            $this->logger->info('Hmac header is missing.');
            throw new HttpException(401);
        }

        $body = $request->getContent();
        if ($body === '' || $body === '0') {
            $this->logger->info('Request body is missing.');
            throw new HttpException(400);
        }

        if (! hash_equals($hmacHeader, hash_hmac('sha256', $body, $secret))) {
            $this->logger->info('Hmac mismatch.');
            throw new HttpException(403);
        }
    }

    private function validateSecretRequest(Request $request, string $secret): void
    {
        $secret = $this->getConfigurationSecret();

        if (! $this->requestAuthorizationService->isAuthorized($request, $secret)) {
            $this->logger->info('Request is not authorized to access the metrics endpoint.');
            throw new HttpException(403);
        }
    }

    private function getConfigurationSecret(): string
    {
        $secret = $this->systemConfigService->get(MobiMamoConnector::CONFIG_KEY_SECRET);
        if (! is_string($secret)) {
            // Can only happen, when we change our config template or Shopware itself screws up.
            $this->logger->error('Configuration Secret is not a string.', [
                'receivedType' => gettype($secret),
            ]);
            throw new HttpException(500);
        }

        return $secret;
    }
}
