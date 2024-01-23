<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Service;

class VersionFilterService
{
    /**
     * Remove all release candidates from the given array of versions.
     *
     * Note: this currently does not do proper semver parsing, and only checks for the string 'rc' in the version.
     *
     * @param array<string> $versions
     * @return array<string>
     */
    public function removeReleaseCandidates(array $versions): array
    {
        return array_values(
            array_filter(
                $versions,
                static fn ($version) => ! str_contains(strtolower($version), 'rc')
            )
        );
    }
}
