<?php

declare(strict_types=1);

namespace App\Core\Domain\Application\CQRS;

use App\Core\Domain\UseCase\Query;

interface QueryBus
{
    public function fetch(Query $query): mixed;
}
