<?php

namespace MobilisticsGmbH\MamoConnector\Tests;

use MobilisticsGmbH\MamoConnector\Service\RequestAuthorizationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RequestAuthorizationServiceTest extends TestCase
{
    private const TESTING_SECRET = "dummy-testing-secret";

    public function testAuthorizedRequestWhenSecretMatches(): void
    {
        $request = new Request([
            "secret" => static::TESTING_SECRET,
        ]);

        $service = new RequestAuthorizationService();
        $isAuthorized = $service->isAuthorized($request, static::TESTING_SECRET);
        static::assertTrue($isAuthorized);
    }

    public function testRequestIsUnauthorizedWhenSecretDoesNotMatch(): void
    {
        $request = new Request([
            "secret" => static::TESTING_SECRET. "-differs",
        ]);

        $service = new RequestAuthorizationService();
        $isAuthorized = $service->isAuthorized($request, static::TESTING_SECRET);
        static::assertFalse($isAuthorized);
    }

    public function testRequestIsUnauthorizedWithoutSecret(): void
    {
        $request = new Request();
        $service = new RequestAuthorizationService();
        $isAuthorized = $service->isAuthorized($request, static::TESTING_SECRET);
        static::assertFalse($isAuthorized);
    }
}
