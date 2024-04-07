<?php

declare(strict_types=1);

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;

use function Castor\import;
use function Castor\io;

import(__DIR__.'/castor/analysis.php');
import(__DIR__.'/castor/composer.php');
import(__DIR__.'/castor/database.php');
import(__DIR__.'/castor/docker.php');
import(__DIR__.'/castor/fix.php');
import(__DIR__.'/castor/symfony.php');
import(__DIR__.'/castor/tests.php');

#[AsTask(aliases: ['build'], name: 'build', namespace: 'tools', description: 'Build the project')]
function build(#[AsOption] ?string $env = null): void
{
    io()->title('Build the project');
    dockerBuild();
    depsInstall();
    if (null === $env) {
        prepare();
        prepare('test');
    } else {
        prepare($env);
    }
}

#[AsTask(aliases: ['install'], name: 'install', namespace: 'tools', description: 'Install the project')]
function install(#[AsOption] ?string $env = null): void
{
    io()->title('Install the project');
    build($env);
    serverPrepare();
    start();
}

#[AsTask(aliases: ['start'], name: 'start', namespace: 'tools', description: 'Start the project')]
function start(): void
{
    io()->title('Start the project');
    dockerStop();
    serverStop();
    dockerStart();
    serverStart();
}

#[AsTask(aliases: ['stop'], name: 'stop', namespace: 'tools', description: 'Stop the project')]
function stop(): void
{
    io()->title('Stop the project');
    dockerStop();
    serverStop();
}

#[AsTask(aliases: ['reset'], name: 'reset', namespace: 'tools', description: 'Reset the project')]
function resetProject(): void
{
    io()->title('Reset the project');
    dockerDown();
    serverStop();
    install();
}

#[AsTask(aliases: ['qa'], name: 'quality', namespace: 'tools', description: 'Run quality analysis')]
function quality(): void
{
    io()->title('Run quality analysis');
    tests();
    analysis();
}
