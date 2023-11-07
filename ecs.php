<?php

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {

    $ecsConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    $ecsConfig->skip([
        __DIR__ . "/tests/TestBootstrap.php",
        __DIR__ . "/tests/TestBootstrapper.php",
        __DIR__ . "/tests/Support/StorefrontControllerTestBehaviour.php",
    ]);

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::STRICT,
        SetList::CLEAN_CODE,
        SetList::COMMON,
    ]);
};
