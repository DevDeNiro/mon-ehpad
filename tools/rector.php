<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../config',
        __DIR__ . '/../migrations',
        __DIR__ . '/../public',
        __DIR__ . '/../src',
        __DIR__ . '/../tests',
    ])
    ->withParallel(maxNumberOfProcess: 8)
    ->withCache(__DIR__ . '/var/.rector_cache')
    ->withPhpSets(php83: true)
    ->withAttributesSets(symfony: true, doctrine: true)
    ->withPreparedSets(true, true, true, true, true, true, true, true, true);
