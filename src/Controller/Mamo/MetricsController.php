<?php

namespace MobilisticsGmbH\MamoConnector\Controller\Mamo;

use MobilisticsGmbH\MamoConnector\MobiMamoConnector;
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
    ) {
    }

    #[Route(path: '/mamo-connector/metrics', name: 'frontend.mamo-connector.metrics', defaults: [
        '_routeScope' => ['storefront'],
    ], methods: ['GET'])]
    public function indexAction(Request $request): Response
    {
        $secret = $request->query->get("secret");
        if (!is_string($secret)) {
            throw new HttpException(400);
        }

        $configSecret = $this->systemConfigService->get(MobiMamoConnector::PLUGIN_IDENTIFIER . ".config.secret");
        if (!is_string($configSecret)) {
            throw new HttpException(500, "No secret configured");
        }

        if ($secret !== $configSecret) {
            throw new HttpException(403, "Invalid Secret");
        }

        return new Response();
    }
}
