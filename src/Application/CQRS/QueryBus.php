<?php

declare(strict_types=1);

namespace App\Application\CQRS;

use App\Application\CQRS\Message\Query;

interface QueryBus
{
    public function fetch(Query $query): mixed;
}
