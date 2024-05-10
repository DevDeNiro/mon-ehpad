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
    ->withPreparedSets(true, true, true, true, true, false, true, true, true);
