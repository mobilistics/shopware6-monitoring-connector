<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

use Symfony\Component\HttpFoundation\Request;

class RequestAuthorizationService
{
    public function isAuthorized(Request $request, string $secret): bool
    {
        $requestSecret = $request->query->get('secret');
        if (! is_string($requestSecret)) {
            return false;
        }

        return $requestSecret === $secret;
    }
}
