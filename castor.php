<?php

declare(strict_types=1);

use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;

use function Castor\io;
use function Castor\run;

#[AsTask(name: 'install', description: 'Install the project')]
function install(#[AsOption] ?string $env = null): void
{
    io()->title('Install the project');
    depsInstall();
    if (null === $env) {
        db();
        db('test');
    } else {
        db($env);
    }
}

#[AsTask(name: 'deps:update', description: 'Update dependencies')]
function depsUpdate(): void
{
    io()->title('Update dependencies');
    run('composer update', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'deps:install', description: 'Install dependencies')]
function depsInstall(): void
{
    io()->title('Install dependencies');
    run('composer install', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'cache:clear', description: 'Clear cache')]
function cacheClear(#[AsOption] ?string $env = 'dev'): void
{
    io()->title('Clear cache');
    run('php bin/console cache:clear', ['XDEBUG_MODE' => 'off', 'APP_ENV' => $env]);
}

#[AsTask(name: 'cache:warmup', description: 'Warm up cache')]
function cacheWarmup(#[AsOption] ?string $env = 'dev'): void
{
    io()->title('Warm up cache');
    run('php bin/console cache:warmup', ['XDEBUG_MODE' => 'off', 'APP_ENV' => $env]);
}

#[AsTask(name: 'db', description: 'Create database schema and load fixtures')]
function db(#[AsOption] ?string $env = 'dev'): void
{
    dbSchema($env);
    dbFixtures($env);
}

#[AsTask(name: 'db:schema', description: 'Create database schema')]
function dbSchema(#[AsOption] ?string $env = 'dev'): void
{
    io()->title('Create database schema');
    run('php bin/console doctrine:database:drop --if-exists -f', ['XDEBUG_MODE' => 'off', 'APP_ENV' => $env]);
    run('php bin/console doctrine:database:create', ['XDEBUG_MODE' => 'off', 'APP_ENV' => $env]);
    run('php bin/console doctrine:migration:migrate --no-interaction --allow-no-migration', ['XDEBUG_MODE' => 'off', 'APP_ENV' => $env]);
}

#[AsTask(name: 'db:migration', description: 'Create new migration')]
function dbMigration(): void
{
    io()->title('Create new migration');
    run('php bin/console make:migration', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'db:fixtures', description: 'Load fixtures')]
function dbFixtures(#[AsOption] ?string $env = 'dev'): void
{
    io()->title('Load fixtures');
    run('php bin/console doctrine:fixtures:load -n', ['XDEBUG_MODE' => 'off', 'APP_ENV' => $env]);
}

#[AsTask(name: 'qa:tests', description: 'Run all tests')]
function qaTests(): void
{
    io()->title('Run all tests');
    run('php bin/simple-phpunit -c tools/phpunit.xml --no-coverage', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:tests:coverage', description: 'Run all tests with coverage')]
function qaTestsWithCoverage(): void
{
    io()->title('Run all tests with coverage');
    run('php bin/simple-phpunit -c tools/phpunit.xml');
}

#[AsTask(name: 'qa:tests:unit', description: 'Run unit tests')]
function qaUnitTests(): void
{
    io()->title('Run unit tests');
    run('php bin/simple-phpunit --testsuite=unit -c tools/phpunit.xml --no-coverage', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:tests:component', description: 'Run component tests')]
function qaComponentTests(): void
{
    io()->title('Run component tests');
    run('php bin/simple-phpunit --testsuite=component -c tools/phpunit.xml --no-coverage', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:tests:integration', description: 'Run integration tests')]
function qaIntegrationTests(): void
{
    io()->title('Run integration tests');
    run('php bin/simple-phpunit --testsuite=integration -c tools/phpunit.xml --no-coverage', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:analysis', description: 'Run quality analysis')]
function qaAnalysis(): void
{
    io()->title('Run quality analysis');
    qaPhpStan();
    qaPhpCsFixer();
    qaPhpMd();
    qaComposer();
    qaDoctrine();
    qaTwig();
    qaYaml();
    qaContainer();
    qaSecurity();
}

#[AsTask(name: 'qa:analysis:phpstan', description: 'Run PHPStan')]
function qaPhpStan(): void
{
    io()->title('Run PHPStan');
    run('php bin/phpstan analyse -c tools/phpstan.neon', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:analysis:php-cs-fixer', description: 'Run PHP CS Fixer (dry run)')]
function qaPhpCsFixer(): void
{
    io()->title('Run PHP CS Fixer (dry run)');
    run('php bin/php-cs-fixer fix --dry-run --config=tools/php-cs-fixer.php', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:analysis:phpmd', description: 'Run PHPMD')]
function qaPhpMd(): void
{
    io()->title('Run PHPMD');
    run('php bin/phpmd src text tools/phpmd.xml', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:analysis:composer', description: 'Run Composer lint')]
function qaComposer(): void
{
    io()->title('Run Composer lint');
    run('composer valid', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:analysis:doctrine', description: 'Run Doctrine lint')]
function qaDoctrine(): void
{
    io()->title('Run Doctrine lint');
    run('php bin/console doctrine:schema:valid --skip-sync', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:analysis:twig', description: 'Run Twig lint')]
function qaTwig(): void
{
    io()->title('Run Twig lint');
    run('php bin/console lint:twig templates', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:analysis:yaml', description: 'Run Yaml lint')]
function qaYaml(): void
{
    io()->title('Run Yaml lint');
    run('php bin/console lint:yaml config --parse-tags', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:analysis:container', description: 'Run Container lint')]
function qaContainer(): void
{
    io()->title('Run Container lint');
    run('php bin/console lint:container', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'qa:analysis:security', description: 'Run Security check')]
function qaSecurity(): void
{
    io()->title('Run Security check');
    run('symfony check:security', ['XDEBUG_MODE' => 'off']);
}

#[AsTask(name: 'fix:php-cs-fixer', description: 'Run PHP CS Fixer')]
function fixPhpCsFixer(): void
{
    io()->title('Run PHP CS Fixer');
    run('php bin/php-cs-fixer fix --config=tools/php-cs-fixer.php', ['XDEBUG_MODE' => 'off']);
}
