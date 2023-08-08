<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Tests\Service;

use MobilisticsGmbH\MamoConnector\Service\RequestAuthorizationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestAuthorizationServiceTest extends TestCase
{
    public function testRequestIsAuthorizedWhenTheSecretFromTheQueryMatches(): void
    {
        $request = new Request();
        $request->query->set('secret', 'random-testing-secret');

        $requestAuthorizationService = new RequestAuthorizationService();
        $isAuthorized = $requestAuthorizationService->isAuthorized($request, 'random-testing-secret');
        static::assertTrue($isAuthorized);
    }

    public function testRequestIsNotAuthorizedWhenTheSecretFromTheQueryDoesNotMatch(): void
    {
        $request = new Request();
        $request->query->set('secret', 'random-testing-secret');

        $requestAuthorizationService = new RequestAuthorizationService();
        $isAuthorized = $requestAuthorizationService->isAuthorized($request, 'another-random-testing-secret');
        static::assertFalse($isAuthorized);
    }

    public function testWithoutSecret(): void
    {
        $request = new Request();

        $requestAuthorizationService = new RequestAuthorizationService();
        $isAuthorized = $requestAuthorizationService->isAuthorized($request, 'another-random-testing-secret');
        static::assertFalse($isAuthorized);
    }

    public function testWithFalseSecret(): void
    {
        $request = new Request();
        $request->query->set('secret', false);

        $requestAuthorizationService = new RequestAuthorizationService();
        $isAuthorized = $requestAuthorizationService->isAuthorized($request, 'another-random-testing-secret');
        static::assertFalse($isAuthorized);
    }
}
