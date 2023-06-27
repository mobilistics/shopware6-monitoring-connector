<?php

namespace MobilisticsGmbH\MamoConnector\Controller\Mamo;

use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MetricsController extends StorefrontController
{
    #[Route(path: '/mamo-connector/metrics', name: 'frontend.mamo-connector.metrics', defaults: [
        '_routeScope' => ['storefront'],
    ], methods: ['GET'])]
    public function indexAction(): Response
    {
        return new Response();
    }
}
