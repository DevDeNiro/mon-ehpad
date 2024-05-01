<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Entity;

use App\Core\Domain\CQRS\Event;
use App\Core\Domain\CQRS\EventBus;

trait EventTrait
{
    private ?EventBus $eventBus = null;

    /**
     * @var array<Event>
     */
    private array $eventsQueue = [];

    public function setEventBus(EventBus $eventBus): void
    {
        $this->eventBus = $eventBus;

        foreach ($this->eventsQueue as $eventQueue) {
            $this->dispatch($eventQueue);
        }

        $this->eventsQueue = [];
    }

    public function dispatch(Event $event): void
    {
        if ($this->eventBus !== null) {
            $this->eventBus->dispatch($event);

            return;
        }

        $this->eventsQueue[] = $event;
    }
}
