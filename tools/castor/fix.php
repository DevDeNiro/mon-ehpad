<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;
use function Castor\io;
use function Castor\run;

#[AsTask(aliases: ['fix'], name: 'all', namespace: 'fix', description: 'Run all fixers')]
function fix(): void
{
    io()->title('Run all fixers');
}

#[AsTask(name: 'ecs', namespace: 'fix', description: 'Run Easy Coding Standard')]
function fixEcs(): void
{
    io()->title('Run Easy Coding Standard');
    run('php bin/ecs --fix --config=tools/ecs.php', [
        'XDEBUG_MODE' => 'off',
    ]);
}

#[AsTask(name: 'rector', namespace: 'fix', description: 'Run Rector')]
function fixRector(): void
{
    io()->title('Run Rector');
    run('php bin/rector process --config tools/rector.php', [
        'XDEBUG_MODE' => 'off',
    ]);
}
