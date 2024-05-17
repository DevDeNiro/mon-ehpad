<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Infrastructure\Symfony\CQRS;

use App\Application\CQRS\EventBus;
use App\Application\CQRS\Message\Event;

final class FakeEventBus implements EventBus
{
    /**
     * @var Event[]
     */
    public array $events = [];

    public function dispatch(Event $event): void
    {
        $this->events[] = $event;
    }
}
