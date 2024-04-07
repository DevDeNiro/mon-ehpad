<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Factory;

use App\Core\Domain\CQRS\EventBus;

abstract class Factory
{
    public function __construct(protected EventBus $eventBus)
    {
    }
}
