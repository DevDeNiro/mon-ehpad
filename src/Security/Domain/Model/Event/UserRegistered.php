<?php

declare(strict_types=1);

namespace App\Security\Domain\Model\Event;

use App\Core\Domain\CQRS\Event;
use App\Core\Domain\Model\ValueObject\Identifier;

final readonly class UserRegistered implements Event
{
    public function __construct(
        private Identifier $identifier
    ) {
    }

    public function id(): Identifier
    {
        return $this->identifier;
    }
}
