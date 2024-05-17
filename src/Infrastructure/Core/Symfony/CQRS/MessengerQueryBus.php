<?php

declare(strict_types=1);

namespace App\Infrastructure\Core\Symfony\CQRS;

use App\Application\CQRS\Message\Query;
use App\Application\CQRS\QueryBus;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerQueryBus implements QueryBus
{
    use HandleTrait {
        handle as handleQuery;
    }

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    public function fetch(Query $query): mixed
    {
        return $this->handleQuery($query);
    }
}
