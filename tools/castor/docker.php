<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;

use function Castor\io;
use function Castor\run;

#[AsTask(name: 'build', namespace: 'docker', description: 'Docker build')]
function dockerBuild(): void
{
    io()->title('Docker build');
    run('docker compose -f "docker-compose.yml" up -d --build');
}

#[AsTask(name: 'start', namespace: 'docker', description: 'Docker start')]
function dockerStart(): void
{
    io()->title('Docker start');
    run('docker compose up -d --remove-orphans');
}

#[AsTask(name: 'stop', namespace: 'docker', description: 'Docker stop')]
function dockerStop(): void
{
    io()->title('Docker stop');
    run('docker compose stop');
}

#[AsTask(name: 'down', namespace: 'docker', description: 'Docker down')]
function dockerDown(): void
{
    io()->title('Docker down');
    run('docker compose down --volumes --remove-orphans');
}
