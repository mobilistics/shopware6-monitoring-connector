<?php

namespace MobilisticsGmbH\MamoConnector\Service;

use Symfony\Component\HttpFoundation\Request;

class RequestAuthorizationService
{
    public function isAuthorized(Request $request, string $secret): bool
    {
        $requestSecret = $request->query->get("secret");
        if (!is_string($requestSecret)) {
            return false;
        }

        if ($requestSecret !== $secret) {
            return false;
        }

        return true;
    }
}