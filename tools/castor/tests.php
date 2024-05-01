<?php

declare(strict_types=1);

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use function Castor\io;
use function Castor\run;

#[AsTask(aliases: ['tests'], name: 'all', namespace: 'tests', description: 'Run all tests')]
function tests(#[AsOption] bool $noCoverage = false): void
{
    io()->title('Run all tests');
    unitTests($noCoverage);
    componentTests($noCoverage);
    integrationTests($noCoverage);
}

#[AsTask(aliases: ['unit'], name: 'unit', description: 'Run unit tests')]
function unitTests(#[AsOption] bool $noCoverage = false): void
{
    io()->title('Run unit tests');
    run('php bin/phpunit --testdox --testsuite=unit -c tools/phpunit.xml', [
        'XDEBUG_MODE' => $noCoverage ? 'off' : 'coverage',
    ]);
}

#[AsTask(aliases: ['component'], name: 'component', description: 'Run component tests')]
function componentTests(#[AsOption] bool $noCoverage = false): void
{
    io()->title('Run component tests');
    run('php bin/phpunit --testdox --testsuite=component -c tools/phpunit.xml', [
        'XDEBUG_MODE' => $noCoverage ? 'off' : 'coverage',
    ]);
}

#[AsTask(aliases: ['integration'], name: 'integration', description: 'Run integration tests')]
function integrationTests(#[AsOption] bool $noCoverage = false): void
{
    io()->title('Run integration tests');
    run('php bin/phpunit --testdox --testsuite=integration -c tools/phpunit.xml', [
        'XDEBUG_MODE' => $noCoverage ? 'off' : 'coverage',
    ]);
}
