<?php

declare(strict_types=1);

use Castor\Attribute\AsArgument;
use Castor\Attribute\AsTask;

use function Castor\io;
use function Castor\run;

#[AsTask(aliases: ['pull'], name: 'pull', namespace: 'git', description: 'Git pull')]
function pull(#[AsArgument] string $branch = 'main'): void
{
    io()->title('Git pull');
    run(['git', 'pull', 'origin', $branch]);
}

#[AsTask(aliases: ['cz'], name: 'cz', namespace: 'git', description: 'Git cz')]
function cz(): void
{
    io()->title('Git cz');
    run('git add .');
    run('git cz');
}
