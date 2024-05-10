<?php

declare(strict_types=1);

namespace Tests\Fixtures\Core\Infrastructure\Symfony\CQRS;

use App\Core\Domain\Application\CQRS\EventBus;
use App\Core\Domain\Application\CQRS\Message\Event;

final class FakeEventBus implements EventBus
{
    /**
     * @var array<Event>
     */
    private array $events = [];

    public function dispatch(Event $event): void
    {
        $this->events[] = $event;
    }

    /**
     * @return array<Event>
     */
    public function events(): array
    {
        return $this->events;
    }
}
