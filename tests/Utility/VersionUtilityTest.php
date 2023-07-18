<?php

namespace MobilisticsGmbH\MamoConnector\Tests\Utility;

use MobilisticsGmbH\MamoConnector\Utility\VersionUtility;
use PHPUnit\Framework\TestCase;

class VersionUtilityTest extends TestCase
{

    public function testConvertVersionToInteger()
    {
        $this->assertEquals(4012003, VersionUtility::convertVersionToInteger('4.12.3'));
        $this->assertEquals(4012000, VersionUtility::convertVersionToInteger('4.12.0'));
        $this->assertEquals(4000000, VersionUtility::convertVersionToInteger('4.0.0'));
    }

    public function testConvertIntegerToVersionNumber()
    {
        $this->assertEquals('4.12.3', VersionUtility::convertIntegerToVersionNumber('4012003'));
        $this->assertEquals('4.12.0', VersionUtility::convertIntegerToVersionNumber('4012000'));
        $this->assertEquals('4.0.0', VersionUtility::convertIntegerToVersionNumber('4000000'));
    }
}
