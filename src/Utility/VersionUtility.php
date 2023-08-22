<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Utility;

class VersionUtility
{
    public static function convertVersionToInteger(string $versionNumber): int
    {
        $versionParts = explode('.', $versionNumber);
        $version = $versionParts[0];
        $versionPartsCount = count($versionParts);
        for ($i = 1; $i < $versionPartsCount; ++$i) {
            if ($versionParts[$i] !== '') {
                $version .= str_pad((string) (int) $versionParts[$i], 3, '0', STR_PAD_LEFT);
            } else {
                $version .= '000';
            }
        }

        return (int) $version;
    }

    /**
     * Returns the cleaned up version number (string) from an integer, e.G. 4012003 -> '4.12.3'
     * Specify the amount of parts you want to extract from the integer. Default is 3 to follow SemVer.
     */
    public static function convertIntegerToVersionNumber(string $versionInteger, int $partCount = 3): string
    {
        $versionString = str_pad($versionInteger, $partCount * 3, '0', STR_PAD_LEFT);
        $parts = [];

        for ($i = 0; $i < $partCount; ++$i) {
            $parts[] = (int) substr($versionString, $i * 3, 3);
        }

        return implode('.', $parts);
    }
}
