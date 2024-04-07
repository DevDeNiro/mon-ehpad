<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Entity;

use App\Core\Domain\CQRS\Event;
use App\Core\Domain\CQRS\EventBus;

interface EventsInterface
{
    public function setEventBus(EventBus $eventBus): void;

    public function dispatch(Event $event): void;
}
