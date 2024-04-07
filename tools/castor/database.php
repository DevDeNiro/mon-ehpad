<?php

declare(strict_types=1);

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;

use function Castor\io;
use function Castor\run;

#[AsTask(aliases: ['db'], name: 'prepare', namespace: 'database', description: 'Create database schema and load fixtures')]
function prepare(#[AsOption] ?string $env = 'dev'): void
{
    dbSchema($env);
    dbFixtures($env);
}

#[AsTask(aliases: ['schema'], name: 'schema', namespace: 'database', description: 'Create database schema')]
function dbSchema(#[AsOption] ?string $env = 'dev'): void
{
    io()->title('Create database schema');
    run('php bin/console doctrine:database:drop --if-exists -f', ['XDEBUG_MODE' => 'off', 'APP_ENV' => $env]);
    run('php bin/console doctrine:database:create', ['XDEBUG_MODE' => 'off', 'APP_ENV' => $env]);
    run('php bin/console doctrine:migration:migrate --no-interaction --allow-no-migration', ['XDEBUG_MODE' => 'off', 'APP_ENV' => $env]);
}

#[AsTask(aliases: ['migration'], name: 'migration', namespace: 'database', description: 'Create new migration')]
function dbMigration(): void
{
    io()->title('Create new migration');
    run('php bin/console make:migration', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(aliases: ['fixtures'], name: 'fixtures', namespace: 'database', description: 'Load fixtures')]
function dbFixtures(#[AsOption] ?string $env = 'dev'): void
{
    io()->title('Load fixtures');
    run('php bin/console doctrine:fixtures:load -n', ['XDEBUG_MODE' => 'off', 'APP_ENV' => $env]);
}
