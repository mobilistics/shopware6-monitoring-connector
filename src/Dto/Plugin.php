<?php

namespace MobilisticsGmbH\MamoConnector\Dto;

class Plugin
{
    public function __construct(
        public string $technicalName,
        public string $version,
    ) {
    }
}
