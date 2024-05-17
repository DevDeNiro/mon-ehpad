<?php

declare(strict_types=1);

namespace App\Application\CQRS;

use App\Application\CQRS\Message\Command;

interface CommandBus
{
    public function execute(Command $command): mixed;
}
