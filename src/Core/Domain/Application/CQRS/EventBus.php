<?php

declare(strict_types=1);

namespace App\Core\Domain\Application\CQRS;

use App\Core\Domain\Application\CQRS\Message\Event;

interface EventBus
{
    public function dispatch(Event $event): void;
}
