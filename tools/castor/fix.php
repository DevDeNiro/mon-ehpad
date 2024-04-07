<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;

use function Castor\io;
use function Castor\run;

#[AsTask(aliases: ['fix'], name: 'all', namespace: 'fix', description: 'Run all fixers')]
function fix(): void
{
    io()->title('Run all fixers');
    fixPhpCsFixer();
}

#[AsTask(name: 'php-cs-fixer', namespace: 'fix', description: 'Run PHP CS Fixer')]
function fixPhpCsFixer(): void
{
    io()->title('Run PHP CS Fixer');
    run('php bin/php-cs-fixer fix --config=tools/php-cs-fixer.php', ['XDEBUG_MODE' => 'off']);
}
