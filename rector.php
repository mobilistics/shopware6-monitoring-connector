<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $rectorConfig->skip([
        __DIR__ . "/tests/TestBootstrap.php",
        __DIR__ . "/tests/TestBootstrapper.php",
        __DIR__ . "/tests/Support/StorefrontControllerTestBehaviour.php",
    ]);

    $rectorConfig->sets([
        // PHP Version set list
        SetList::PHP_81,

        // Built-In set lists
        SetList::DEAD_CODE,
        SetList::CODING_STYLE,
        SetList::CODE_QUALITY,
    ]);
};
