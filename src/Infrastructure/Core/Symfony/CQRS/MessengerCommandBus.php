<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Symfony\CQRS;

use App\Application\CQRS\CommandBus;
use App\Application\CQRS\Message\Command;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerCommandBus implements CommandBus
{
    use HandleTrait {
        handle as handleQuery;
    }

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function execute(Command $command): mixed
    {
        return $this->handleQuery($command);
    }
}
