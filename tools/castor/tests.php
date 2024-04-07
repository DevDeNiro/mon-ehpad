<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;

use function Castor\io;
use function Castor\run;

#[AsTask(aliases: ['tests'], name: 'all', namespace: 'tests', description: 'Run all tests')]
function tests(): void
{
    io()->title('Run all tests');
    unitTests();
    componentTests();
    integrationTests();
}

#[AsTask(aliases: ['coverage'], name: 'coverage', description: 'Run all tests with coverage')]
function testsWithCoverage(): void
{
    io()->title('Run all tests with coverage');
    run('php bin/simple-phpunit -c tools/phpunit.xml');
}

#[AsTask(aliases: ['unit'], name: 'unit', description: 'Run unit tests')]
function unitTests(): void
{
    io()->title('Run unit tests');
    run('php bin/simple-phpunit --testdox --testsuite=unit -c tools/phpunit.xml --no-coverage', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(aliases: ['component'], name: 'component', description: 'Run component tests')]
function componentTests(): void
{
    io()->title('Run component tests');
    run('php bin/simple-phpunit --testdox --testsuite=component -c tools/phpunit.xml --no-coverage', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(aliases: ['integration'], name: 'integration', description: 'Run integration tests')]
function integrationTests(): void
{
    io()->title('Run integration tests');
    run('php bin/simple-phpunit --testdox --testsuite=integration -c tools/phpunit.xml --no-coverage', ['XDEBUG_MODE' => 'off']);
}
