<?php

declare(strict_types=1);

namespace MobilisticsGmbH\MamoConnector\Tests\Utility;

use MobilisticsGmbH\MamoConnector\Utility\VersionUtility;
use PHPUnit\Framework\TestCase;

class VersionUtilityTest extends TestCase
{
    public function testConvertVersionToInteger(): void
    {
        $this->assertEquals(4012003, VersionUtility::convertVersionToInteger('4.12.3'));
        $this->assertEquals(4012000, VersionUtility::convertVersionToInteger('4.12.0'));
        $this->assertEquals(4000000, VersionUtility::convertVersionToInteger('4.0.0'));
    }

    public function testConvertIntegerToVersionNumber(): void
    {
        $this->assertEquals('4.12.3', VersionUtility::convertIntegerToVersionNumber('4012003'));
        $this->assertEquals('4.12.0', VersionUtility::convertIntegerToVersionNumber('4012000'));
        $this->assertEquals('4.0.0', VersionUtility::convertIntegerToVersionNumber('4000000'));
    }

    public function testFourPartVersionNumber(): void
    {
        $this->assertEquals(6004020002, VersionUtility::convertVersionToInteger('6.4.20.2'));
        $this->assertEquals('6.4.20.2', VersionUtility::convertIntegerToVersionNumber('6004020002', 4));
    }

    public function testFourPartVersionNumberWithLastNumberZero(): void
    {
        $this->assertEquals(6004020000, VersionUtility::convertVersionToInteger('6.4.20.0'));
        $this->assertEquals('6.4.20.0', VersionUtility::convertIntegerToVersionNumber('6004020000', 4));
    }
}
