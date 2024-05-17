<?php

declare(strict_types=1);

namespace App\Application\CQRS;

use App\Application\CQRS\Message\Event;

interface EventBus
{
    public function dispatch(Event $event): void;
}
