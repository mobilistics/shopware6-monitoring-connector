<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Controller\Mamo;

use MobilisticsGmbH\MamoConnector\MobiMamoConnector;
use MobilisticsGmbH\MamoConnector\Service\ExtensionDataProvider;
use MobilisticsGmbH\MamoConnector\Service\RequestAuthorizationService;
use MobilisticsGmbH\MamoConnector\Utility\VersionUtility;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
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
    ) {
    }

    #[Route(path: '/mamo-connector/metrics', name: 'frontend.mamo-connector.metrics', defaults: [
        '_routeScope' => ['storefront'],
    ], methods: ['GET'])]
    public function indexAction(Request $request, Context $context): Response
    {
        $secret = $this->systemConfigService->get(MobiMamoConnector::PLUGIN_IDENTIFIER . '.config.secret');
        if (! is_string($secret)) {
            // Can only happen, when we change our config template or Shopware itself screws up.
            $this->logger->error('Configuration Secret is not a string.', [
                'receivedType' => gettype($secret),
            ]);
            throw new HttpException(500);
        }

        if (! $this->requestAuthorizationService->isAuthorized($request, $secret)) {
            $this->logger->info('Request is not authorized to access the metrics endpoint.');
            throw new HttpException(403);
        }

        $registry = new CollectorRegistry(new InMemory());
        $registry->getOrRegisterGauge('mamo', 'shopware6_version', 'Shopware 6 Version in numeric representation')
            ->set(
                VersionUtility::convertVersionToInteger('6.5.2.1'),
            );

        foreach ($this->extensionDataProvider->loadExtensionData() as $plugin) {
            $version = VersionUtility::convertVersionToInteger($plugin->version);

            $registry->getOrRegisterGauge('mamo', 'shopware6_plugin', 'Shopware 6 Plugin Version', ['technicalName', 'currentVersion'])
                ->set(
                    $version,
                    [
                        'technicalName' => $plugin->technicalName,
                        'currentVersion' => (string) $version,
                    ],
                );
        }

        $renderer = new RenderTextFormat();
        $result = $renderer->render($registry->getMetricFamilySamples());

        return new Response($result, 200, [
            'Content-type' => RenderTextFormat::MIME_TYPE,
        ]);
    }
}
