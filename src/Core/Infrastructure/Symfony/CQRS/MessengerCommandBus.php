<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\CQRS;

use App\Core\Domain\UseCase\Command;
use App\Core\Domain\Application\CQRS\CommandBus;
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
