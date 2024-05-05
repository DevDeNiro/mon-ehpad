<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;
use function Castor\io;
use function Castor\run;

#[AsTask(aliases: ['analysis'], name: 'all', namespace: 'analysis', description: 'Run quality analysis')]
function analysis(): void
{
    io()->title('Run quality analysis');
    analysisPhpStan();
    analysisPhpMd();
    analysisComposer();
    analysisDoctrine();
    analysisTwig();
    analysisYaml();
    analysisContainer();
    analysisSecurity();
}

#[AsTask(aliases: ['phpstan'], name: 'phpstan', namespace: 'analysis', description: 'Run PHPStan')]
function analysisPhpStan(): void
{
    io()->title('Run PHPStan');
    run('php bin/phpstan analyse -c tools/phpstan.neon', [
        'XDEBUG_MODE' => 'off',
    ]);
}

#[AsTask(name: 'rector', namespace: 'analysis', description: 'Run Rector')]
function analysisRector(): void
{
    io()->title('Run Rector');
    run('php bin/rector process --dry-run --xdebug --config tools/rector.php');
}

#[AsTask(name: 'ecs', namespace: 'analysis', description: 'Run Easy Coding Standard')]
function analysisEcs(): void
{
    io()->title('Run Easy Coding Standard');
    run('php bin/ecs --config=tools/ecs.php', [
        'XDEBUG_MODE' => 'off',
    ]);
}

#[AsTask(aliases: ['phpmd'], name: 'phpmd', namespace: 'analysis', description: 'Run PHPMD')]
function analysisPhpMd(): void
{
    io()->title('Run PHPMD');
    run('php bin/phpmd src text tools/phpmd.xml', [
        'XDEBUG_MODE' => 'off',
    ]);
}

#[AsTask(name: 'composer', namespace: 'analysis', description: 'Run Composer lint')]
function analysisComposer(): void
{
    io()->title('Run Composer lint');
    run('composer valid', [
        'XDEBUG_MODE' => 'off',
    ]);
}

#[AsTask(name: 'doctrine', namespace: 'analysis', description: 'Run Doctrine lint')]
function analysisDoctrine(): void
{
    io()->title('Run Doctrine lint');
    run('php bin/console doctrine:schema:valid --skip-sync', [
        'XDEBUG_MODE' => 'off',
    ]);
}

#[AsTask(name: 'twig', namespace: 'analysis', description: 'Run Twig lint')]
function analysisTwig(): void
{
    io()->title('Run Twig lint');
    run('php bin/console lint:twig templates', [
        'XDEBUG_MODE' => 'off',
    ]);
}

#[AsTask(name: 'yaml', namespace: 'analysis', description: 'Run Yaml lint')]
function analysisYaml(): void
{
    io()->title('Run Yaml lint');
    run('php bin/console lint:yaml config --parse-tags', [
        'XDEBUG_MODE' => 'off',
    ]);
}

#[AsTask(name: 'container', namespace: 'analysis', description: 'Run Container lint')]
function analysisContainer(): void
{
    io()->title('Run Container lint');
    run('php bin/console lint:container', [
        'XDEBUG_MODE' => 'off',
    ]);
}

#[AsTask(name: 'security', namespace: 'analysis', description: 'Run Security check')]
function analysisSecurity(): void
{
    io()->title('Run Security check');
    run('symfony check:security', [
        'XDEBUG_MODE' => 'off',
    ]);
}
