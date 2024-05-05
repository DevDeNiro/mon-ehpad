<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Domain\UseCase\Event;

trait EventBusAssertionsTrait
{
    public static function assertEventDispatched(Event $event): void
    {
        $serializedEvent = serialize($event);
        $events = static::eventBus()->events();
        self::assertContains($serializedEvent, array_map('serialize', $events));
    }
}
