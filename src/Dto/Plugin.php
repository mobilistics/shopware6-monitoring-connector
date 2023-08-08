<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Dto;

class Plugin
{
    public function __construct(
        public string $name,
        public string $version,
        public string $latestVersion,
    ) {
    }
}
