<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Symfony\CQRS;

use App\Application\CQRS\EventBus;
use App\Application\CQRS\Message\Event;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final readonly class MessengerEventBus implements EventBus
{
    public function __construct(
        private MessageBusInterface $eventBus
    )
    {
    }

    public function dispatch(Event $event): void
    {
        $this->eventBus->dispatch((new Envelope($event))->with(new DispatchAfterCurrentBusStamp()));
    }
}
