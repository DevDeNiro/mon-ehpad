<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\CQRS;

use App\Core\Domain\UseCase\Event;
use App\Core\Domain\Application\CQRS\EventBus;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final readonly class MessengerEventBus implements EventBus
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function dispatch(Event $event): void
    {
        $this->messageBus->dispatch((new Envelope($event))->with(new DispatchAfterCurrentBusStamp()));
    }
}
