<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Symfony\CQRS;

use App\Core\Domain\UseCase\Query;
use App\Core\Domain\Application\CQRS\QueryBus;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerQueryBus implements QueryBus
{
    use HandleTrait {
        handle as handleQuery;
    }

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function fetch(Query $query): mixed
    {
        return $this->handleQuery($query);
    }
}
