<?php

declare(strict_types=1);

namespace App\Core\Domain\Application\CQRS;

use App\Core\Domain\UseCase\Event;

interface EventBus
{
    public function dispatch(Event $event): void;
}
