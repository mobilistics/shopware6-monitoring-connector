<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Dto\ShopwareApi;

class Plugin
{
    public function __construct(
        public string $name,
        public string $version
    ) {
    }
}
