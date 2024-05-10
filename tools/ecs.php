<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/../config',
        __DIR__ . '/../migrations',
        __DIR__ . '/../public',
        __DIR__ . '/../src',
        __DIR__ . '/../tests',
    ])
    ->withParallel(maxNumberOfProcess: 8)
    ->withCache(__DIR__ . '/var/.ecs_cache')
    ->withPreparedSets(psr12: true);
