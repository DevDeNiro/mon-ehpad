<?php

declare(strict_types=1);

namespace App\Core\Domain\Application\CQRS;

use App\Core\Domain\UseCase\Command;

interface CommandBus
{
    public function execute(Command $command): mixed;
}
