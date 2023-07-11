<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Utility;

class VersionUtility
{
    public static function convertVersionToInteger(string $versionNumber): int
    {
        $versionParts = explode('.', $versionNumber);
        $version = $versionParts[0];
        for ($i = 1; $i < 3; ++$i) {
            if ($versionParts[$i] !== '') {
                $version .= str_pad((string) (int) $versionParts[$i], 3, '0', STR_PAD_LEFT);
            } else {
                $version .= '000';
            }
        }

        return (int) $version;
    }

    /**
     * Returns the three part version number (string) from an integer, eg 4012003 -> '4.12.3'
     */
    public static function convertIntegerToVersionNumber(string $versionInteger): string
    {
        $versionString = str_pad($versionInteger, 9, '0', STR_PAD_LEFT);
        $parts = [
            substr($versionString, 0, 3),
            substr($versionString, 3, 3),
            substr($versionString, 6, 3),
        ];
        return (int) $parts[0] . '.' . (int) $parts[1] . '.' . (int) $parts[2];
    }
}
